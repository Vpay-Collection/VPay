clean:
	rm -rf ./dist && mkdir ./dist
changeLog:
	git cliff  --output CHANGELOG.md
build:clean
	./src/cleanphp/release/clean release -v=4.2.0 -n=vpay
	./src/cleanphp/release/clean release -v=4.2.0 -n=vpay_bt -f=build_bt.php