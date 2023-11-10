clean:
	rm -rf ./dist && mkdir ./dist
build:clean
	./src/cleanphp/release/clean release -v=4.2.0 -n=vpay