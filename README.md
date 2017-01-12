# Remote Image Resizer

[![Travis](https://img.shields.io/travis/villeristi/remote-image-resizer.svg)]()
[![license](https://img.shields.io/github/license/villeristi/remote-image-resizer.svg)]()

A simple class which lets you host your own remote image resizer service via simple API.

### Config


### API

---
```
http://[yourdomain.com]/image.php?src=http://remotesite.com/remoteimage.jpg&size=200x100
```
Crops the image to given dimensions (width x height)

---
```
http://[yourdomain.com]/image.php?src=http://remotesite.com/remoteimage.jpg&size=200
```
Crops the image to square by the given dimension (200px)

---
```
http://[yourdomain.com]/image.php?src=http://remotesite.com/remoteimage.jpg&size=200x
```
Resizes the image to 200px width and preserves aspect ratio on height

---
```
http://[yourdomain.com]/image.php?src=http://remotesite.com/remoteimage.jpg&size=x100
```
Resizes the image to 100px height and preserves aspect ratio on width

---