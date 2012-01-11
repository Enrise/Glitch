### Glitch Demo Web Application (release 3.0)

**Before using:**

1.   Go to public/index.php and change ``APP_NAME`` constant into something you like
2.   switch to the correct branch (release 3.0)
3.   Copy or symlink the Zend and Glitch library to ./library

**Installation summary - Linux Unix**

this installs the demo next to Glitch with all libraries symlinked to Glitch.


```
git clone https://github.com/Enrise/Glitch.git
cd Glitch/
git checkout release-3.0
cp demo/WebApplication ../WebApplication && cd ../WebApplication/library/
ln -s ../../Glitch/library/Glitch Glitch
ln -s ../../Glitch/library/Zend Zend
```


*Glitch is brought to you by*

![Enrise Create Web Technology ](http://www.enrise.com/enrise-creative-web-technology.gif)

