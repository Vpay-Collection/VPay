clean:
	rm -rf ../dist && mkdir ../dist

build:clean
	./src/clean release -c -v=4.0.5 -n=vpay

