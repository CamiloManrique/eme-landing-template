#!/usr/bin/env bash
if [ -z "$SSH_DEPLOY_PATH" ]; then
    echo "La variable SSH_DEPLOY_PATH no esta definida"
    exit 1
fi

if [ -z "$CI_REPOSITORY_URL" ]; then
    echo "La variable CI_REPOSITORY_URL no esta definida"
    exit 1
fi

if [ -z "$CI_PROJECT_NAME" ]; then
    echo "La variable CI_PROJECT_NAME no esta definida"
    exit 1
fi

if [ -z "$DEPLOY_USERNAME" ]; then
    echo "La variable DEPLOY_USERNAME no esta definida"
    exit 1
fi

if [ -z "$DEPLOY_TOKEN" ]; then
    echo "La variable DEPLOY_TOKEN no esta definida"
    exit 1
fi

if [ -z "$CI_PROJECT_PATH" ]; then
    echo "La variable CI_PROJECT_PATH no esta definida"
    exit 1
fi


if [ -z "$PHP_EXECUTABLE" ]; then
    PHP_EXECUTABLE="php"
fi


cd $SSH_DEPLOY_PATH

if [ ! -d ".git" ]; then
    echo "No se ha encontrado el repositorio. Clonando..."
    if [ -d $CI_PROJECT_NAME ]; then
        echo "Eliminando carpeta con nombre del repositorio"
        rm -rf $CI_PROJECT_NAME
    fi
    git clone "https://$DEPLOY_USERNAME:$DEPLOY_TOKEN@gitlab.com/$CI_PROJECT_PATH.git"
    echo "Clonado con exito"
    cd $CI_PROJECT_NAME
    echo "Comprimiendo y copiando a la ruta de deploy"
    touch gitlab_ci_backup.tar.gz
    tar --exclude=gitlab_ci_backup.tar.gz -cvzpf gitlab_ci_backup.tar.gz .
    tar -xzpf gitlab_ci_backup.tar.gz -C ../
    echo "Deploy exitoso. Limpiando..."
    cd ..
    rm -rf $CI_PROJECT_NAME
    rm -f gitlab_ci_backup.tar.gz
fi

echo "Realizando git fetch"
git fetch
echo "Checkout de la rama master"
git checkout master
echo "Realizando pull"
git pull

if [ -f "composer.json" ]; then
    echo "Composer.json encontrado"
    if [ -f "composer.lock" ]; then
        echo "Composer.lock encontrado... Instalando dependencias..."
        $PHP_EXECUTABLE composer.phar install
    else
        echo "No hay composer.lock... Actualizando dependencias..."
        $PHP_EXECUTABLE composer.phar update
    fi
fi

if [ -f "phinx.php" ]; then
    echo "Archivo phinx.php encontrado... Ejecutando migraciones"
    vendor/bin/phinx migrate
fi

type npm >/dev/null 2>&1 || { echo >&2 "NPM no se encuentra instalado. No se instalara ningun paquete de NPM."; exit 0; }

if [ -f "package.json" ]; then
    echo "package.json encontrado"
    if [ -f "package-lock.json" ]; then
        echo "package-lock encontrado... Instalando dependencias..."
        npm ci
    else
        echo "No hay package-lock.json... Actualizando dependencias..."
        npm install
    fi
fi

if [ -f "webpack.mix.js" ]; then
    echo "Webpack Mix encontrado... Compilando assets para produccion"
    npm run prod
fi