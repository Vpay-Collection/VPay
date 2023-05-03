release-single-compress: clean
	./clean release -c -s
release: clean
	./clean release
release-compress: clean
	./clean release -c
release-model-compress: clean
	./clean release -c -m -n=php
clean:
	rm -rf ../dist && mkdir ../dist
start:
	./clean start index/main/index
