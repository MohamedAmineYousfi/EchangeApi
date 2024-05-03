#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

DEPLOY_CACHE=$DEPLOY_PATH/deploy_cache
RELEASE_PATH=$DEPLOY_PATH/releases/$BITBUCKET_COMMIT

echo "Deploy to $BITBUCKET_DEPLOYMENT_ENVIRONMENT"

# Deploy build
rsync -rz --delete --links --exclude='.git*' --exclude="/vendor/" --exclude="/node_modules/" --exclude="/bitbucket.*" ./ $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_CACHE/
ssh $DEPLOY_USER@$DEPLOY_HOST "

    set -ex

    if [ -d "$RELEASE_PATH" ];
    then
        echo "Removing: $RELEASE_PATH"
        rm -rf $RELEASE_PATH
    fi

    echo "Running composer:"
    cd $DEPLOY_CACHE
    composer install --optimize-autoloader --no-dev

    echo "Creating release: $RELEASE_PATH"
    rsync -a $DEPLOY_CACHE/ $RELEASE_PATH/

    echo "Linking config and shared storage"
    ln -sf $DEPLOY_PATH/.env $RELEASE_PATH/.env

    if [ -d "$DEPLOY_PATH/storage" ];
    then
        rm -rf $RELEASE_PATH/storage
    else
        mv $RELEASE_PATH/storage $DEPLOY_PATH
    fi

    ln -sf $DEPLOY_PATH/storage $RELEASE_PATH/storage

    echo "Optimize config and migrate database"
    cd $RELEASE_PATH
    /usr/bin/php $RELEASE_PATH/artisan optimize
    /usr/bin/php $RELEASE_PATH/artisan storage:link
    /usr/bin/php $RELEASE_PATH/artisan migrate --force
    /usr/bin/php $RELEASE_PATH/artisan queue:restart
    /usr/bin/php $RELEASE_PATH/artisan app:permissions:sync
    /usr/bin/php $RELEASE_PATH/artisan config:clear

    echo "Linking release to current"
    rm -f $DEPLOY_PATH/current
    ln -sf $RELEASE_PATH $DEPLOY_PATH/current

    echo "Cleanup old releases"
    cd $DEPLOY_PATH/releases && ls -t | tail -n +3 | xargs rm -rf ;
"
