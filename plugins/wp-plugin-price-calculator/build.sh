#zip without nodejs
cd ./nodejs
yarn build
cd ../..
zip ./price-calculator.zip -r price-calculator \
    -x "price-calculator/nodejs/*" price-calculator/build.sh "price-calculator/.git/*" @