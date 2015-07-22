# Get ApiGen.phar
wget http://www.apigen.org/apigen.phar

# Get the boostrap theme
git clone https://github.com/jimmyz/ThemeBootstrap.git

# Generate Api
php apigen.phar generate -s src -d ../gh-pages --access-levels="public" --title="gedcomx-php" --template-config="ThemeBootstrap/src/config.neon"
cd ../gh-pages

# Set identity
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis"

# Add branch
git init
git remote add origin https://${GH_TOKEN}@github.com/FamilySearch/gedcomx-php.git > /dev/null
git checkout -B gh-pages

# Push generated files
git add .
git commit -m "Update docs"
git push origin gh-pages -fq > /dev/null