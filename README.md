# PhoneGap Packager

PhoneGap打包器，支持Android，iOS*平台，采用非编译打包方式，去掉了编译过程，加快了打包速度。

这个版本为轻量单机版。

模板包采用新浪移动云平台模板，支持新浪移动云的所有插件。

# 环境

需要linux，java，php环境支持。

libs/tools/aapt放到/usr/bin/中

# 使用

## 工程创建
php create.php project_name

## 工程打包
php build.android.php project_name [package_name app_name app_version]

php build.ios.php project_name [package_name app_name app_version]

## 工程内文件说明
* -www/ 存放静态页面的目录，PhoneGap源代码目录
* -Default.png iphone启动画面(320×480)
* -Default@2x.png iphone启动画面(640×960)
* -android_small.png android低像素启动画面
* -android_middle.png android中像素启动画面
* -android_big.png android高像素启动画面
* -icon@2x.png 应用图标（114×114）
* -icon.png 应用图标（57×57）
* -icon-72.png 应用图标（72×72）
* -icon.72×72.png 应用图标（72×72）
* -icon.48×48.png 应用图标（48×48）
* -icon.36×36.png 应用图标（36×36）

## 安装包
打包后的安装包在dist目录，Android包为MyApp-release.apk，iOS包为MyApp-release.ipa。

# 注意
iOS平台打包为需要越狱安装的ipa包。