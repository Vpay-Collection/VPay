clean:
	rm -rf ./dist && mkdir ./dist

build:clean
	./src/cleanphp/release/clean release -v=4.1.0 -n=vpay
	./src/cleanphp/release/clean release -v=4.1.0 -n=vpay_bt -f=build_bt.php
	./src/cleanphp/release/clean release -v=4.1.0 -n=vpay_docker -f=build_docker.php