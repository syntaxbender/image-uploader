# HTML5 Javascript Image Uploader

## Usage

First import javascript file.
``` html
<script type="text/javascript" src="assets/js/sbImageUploader.js"></script>
```

Add textbox area inside body tags.
```html
<body>
...
.
.

<div class="sbImageContainer"></div>

.
.
...
</body>
```

Call init method, it will be ready for use.
``` javascript
sbImageUploader.init();
``` 

Inıt method with additional parameters.

``` javascript
sbImageUploader.init();
{
	uploadButtonText : "Yükle",
	startNumber : 3,
	maxImageNumber : 10,
	contentTypeErrorMessage : "Yalnızca jpeg, gif, bmp ve png dosyaları yükleyebilirsiniz.",
	acceptableTypes : ["image/gif", "image/jpeg", "image/png","image/bmp"],
}
``` 
Get uploaded files with get method.

``` javascript
sbImageUploader.get();
``` 

## Notes
Need json array output from backend.
- First element of array indicate as boolean, upload succes or fail.
- Second element of array indicate uploaded image filename.

Example output :
``` javascript
[true,"img.jpg"]
```
## The next commits targets
- Register done function after image uploads.
- Store filename received from backend or client.
- Upload error message from backend.
- Improvement for style.css, right now like a trash.