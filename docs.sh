# Get ApiGen.phar
wget http://www.apigen.org/apigen.phar

# Get the boostrap theme
git clone https://github.com/jimmyz/ThemeBootstrap.git ../ThemeBootstrap

# Generate docs
php apigen.phar generate -s src -d ../docs --access-levels="public" --title="gedcomx-php" --template-config="../ThemeBootstrap/src/config.neon"

# Set identity
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis"

# Switch to gh-pages
git checkout gh-pages

# Clear the current directory so that we don't accidentally
# commit any leftover files from the build or test process
# such as the vendor folder
rm -rf ./*

# Delete old docs and copy in new docs
cp -R ../docs/* ./

# Push generated files
git add .
git commit -m "Update docs"
git push origin gh-pages -q > /dev/null