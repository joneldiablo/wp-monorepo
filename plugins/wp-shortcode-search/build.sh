#zip without nodejs
cd ./nodejs
yarn build
cd ../..
zip ./shortcode-search-products.zip -r shortcode-search-products \
    -x "shortcode-search-products/nodejs/*" shortcode-search-products/build.sh "shortcode-search-products/.git/*" @