release-single-compress: clean
	./clean release -c -s
release: clean
	./clean release
release-compress: clean
	./clean release -c -v=4.0.3 -n=vpay
clean:
	rm -rf ../dist && mkdir ../dist
start:
	./clean start index/main/index
