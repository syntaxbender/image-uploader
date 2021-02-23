var sbImageUploader = {
	uploadButtonText : "Yükle",
	startNumber : 3,
	maxImageNumber : 10,
	contentTypeErrorMessage : "Yalnızca jpeg, gif, bmp ve png dosyaları yükleyebilirsiniz.",
	acceptableTypes : ["image/gif", "image/jpeg", "image/png","image/bmp"],
	imageCollector : {},
	uploadedImages : {},
	lock : false,
	uploadStartButton(text){
		let uploadStartIcon = document.createElement("img");
		uploadStartIcon.setAttribute("src","./assets/img/upload.svg");
		let uploadStartTextSpan = document.createElement("span");
		uploadStartTextSpan.innerHTML = text;
		let uploadButtonBox = document.createElement("div");
		uploadButtonBox.setAttribute("class","uploadButtonBox");
		uploadButtonBox.appendChild(uploadStartIcon);
		uploadButtonBox.appendChild(uploadStartTextSpan);
		return uploadButtonBox;
	},
	uploadDefContainer(index){
		let addIcon = document.createElement("img");
		addIcon.setAttribute("src","./assets/img/add.svg");
		let uploadLabel = document.createElement("label");
		let uploadInput = document.createElement("input");
		uploadInput.setAttribute("data-index",index);
		uploadInput.setAttribute("type","file");
		let uploadDefDiv = document.createElement("div");
		uploadDefDiv.setAttribute("class","imgPreview");
		uploadLabel.appendChild(uploadInput);
		uploadDefDiv.appendChild(addIcon);
		uploadDefDiv.appendChild(uploadLabel);
		return uploadDefDiv;
	},
	uploadImageChoosedContainer(index){
		let deleteIcon = document.createElement("img");
		deleteIcon.setAttribute("src","./assets/img/cancel.svg");
		let deleteIconDiv = document.createElement("div");
		deleteIconDiv.setAttribute("class","deleteImage");
		deleteIconDiv.setAttribute("data-index",index);
		deleteIconDiv.appendChild(deleteIcon);
		let uploadDefDiv = document.createElement("div");
		uploadDefDiv.setAttribute("class","imgPreview");
		uploadDefDiv.appendChild(deleteIconDiv);
		return uploadDefDiv;
	},
	uploadDoneContainer(index){
		let doneIcon = document.createElement("img");
		doneIcon.setAttribute("src","./assets/img/done.svg");
		let deleteIcon = document.createElement("img");
		deleteIcon.setAttribute("src","./assets/img/cancel.svg");
		let doneIconDiv = document.createElement("div");
		doneIconDiv.setAttribute("class","uploadDone");
		let deleteIconDiv = document.createElement("div");
		deleteIconDiv.setAttribute("data-index",index);
		deleteIconDiv.setAttribute("class","deleteImage");
		let uploadDefDiv = document.createElement("div");
		uploadDefDiv.setAttribute("class","imgPreview");
		doneIconDiv.appendChild(doneIcon);
		deleteIconDiv.appendChild(deleteIcon);
		uploadDefDiv.appendChild(doneIconDiv);
		uploadDefDiv.appendChild(deleteIconDiv);
		return uploadDefDiv;
	},
	uploadImage(method, url, parameters = {}, isSync,num){
		let xhr = new XMLHttpRequest();
		xhr.open(method, url, true);
		xhr.onreadystatechange = function () {
			if (this.readyState == 4){
				if(this.status == 200){
					sbImageUploader.uploadDone(true,num,this.responseText);
				}else{
					sbImageUploader.uploadDone(false,num,"hata");
				} 
			}
		};
		xhr.upload.addEventListener("progress", function(e){
			if (e.lengthComputable){
				let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[num];
				const percentComplete = ((e.loaded/e.total)*100);
				imgdiv.innerHTML = "<div class=\"uploadPercentage\"><img class=\"circle-img\" src=\"./assets/img/circle.svg\">"+Math.round(percentComplete)+"%</div>";
			}
		});
		xhr.send(parameters);
	},
	deleteImage(index){
		if(this.imageCollector[index] != null) delete this.imageCollector[index];
		else if(this.uploadedImages[index] != null) delete this.uploadedImages[index];
		let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[index];
		imgdiv.removeAttribute("style");
		imgdiv.innerHTML = this.uploadDefContainer(index).innerHTML;
	},
	uploadDone(status, index, data){
		data = JSON.parse(data);
		if(status === true && data[0] === true){
			this.uploadedImages[index] = data[1];
		}
		delete this.imageCollector[index];
		let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[index];
		imgdiv.innerHTML = this.uploadDoneContainer(index).innerHTML;
		if(Object.keys(this.imageCollector).length<1){
			this.uploadLock(false);
		}
	},
	showimage(element,data,filetype){
		let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview");
		imgdiv[element].innerHTML = this.uploadImageChoosedContainer(element).innerHTML;
		const blob = new Blob([data], {type: filetype});
		imgdiv[element].setAttribute("style","background:url(\""+URL.createObjectURL(blob)+"\") no-repeat center center;")
		const totalImage = Object.keys(this.imageCollector).length+Object.keys(this.uploadedImages).length;
		if(totalImage==imgdiv.length){
			if(totalImage == this.maxImageNumber) return;
			imgdiv[imgdiv.length-1].insertAdjacentHTML("afterend",this.uploadDefContainer(imgdiv.length).outerHTML);
		}
	},
	generateContainer(){
		let buffer = "";
		for (let i=0; i<this.startNumber; i++){
			buffer += this.uploadDefContainer(i).outerHTML;
		}
		buffer += this.uploadStartButton(this.uploadButtonText).outerHTML;
		document.querySelector("div.sbImageContainer").innerHTML = buffer;
	},
	uploadLock(status){
		if(status===true){
			this.lock = true;
			let uploadbutton = document.querySelector("div.sbImageContainer > div.uploadButtonBox");
			uploadbutton.setAttribute("style", "background:#848484; color:rgb(204 204 204);");
			uploadbutton.querySelector("span").innerHTML = "...";
			let input = document.querySelectorAll("div.sbImageContainer > div.imgPreview input");
			for(let i=0; i<input.length; i++){
				input[i].setAttribute("disabled", true);
			}
		}else{
			this.lock=false;
			let uploadbutton = document.querySelector("div.sbImageContainer > div.uploadButtonBox");
			uploadbutton.removeAttribute("style");
			uploadbutton.querySelector("span").innerHTML = this.uploadButtonText;
			let input = document.querySelectorAll("div.sbImageContainer > div.imgPreview input");
			for(let i=0; i<input.length; i++){
				input[i].removeAttribute("disabled");
			}
		}
	},
	get(){
		return this.uploadedImages;
	},
	init(arr){
		if(arr["uploadButtonText"]) this.uploadButtonText = arr["uploadButtonText"];
		if(arr["startNumber"]) this.startNumber = arr["startNumber"];
		if(arr["maxImageNumber"]) this.maxImageNumber = arr["maxImageNumber"];
		if(arr["contentTypeErrorMessage"]) this.contentTypeErrorMessage = arr["contentTypeErrorMessage"];
		if(arr["acceptableTypes"]) this.acceptableTypes = arr["acceptableTypes"];

		window.addEventListener('load', event => {
			this.generateContainer();
			window.addEventListener('change', event => {
				if(event.target.closest("div.sbImageContainer > div.imgPreview input")){
					let input = event.target.closest("div.sbImageContainer > div.imgPreview input");
					if(this.lock===true) return;
					if (input.files && input.files[0]){
						const index = input.getAttribute("data-index");
						const file = input.files[0];
						if(this.acceptableTypes.indexOf(file.type) != -1){
							this.imageCollector[index] = file;
							this.showimage(index,file,file.type);
						}else{
							alert(this.contentTypeErrorMessage);
						}
					}
				}
			});
			window.addEventListener('click', event => {
				if(event.target.closest("div.sbImageContainer > div.uploadButtonBox")){
					if(this.lock === true) return;
					let isEmpty = true;
					for(let i in this.imageCollector){
						if(this.uploadedImages[i] != null) continue;
						let formData = new FormData();
						formData.append('image', this.imageCollector[i]);
						this.uploadImage("POST", "./upload.php",formData, true, i); // beri bak
						isEmpty=false;
					}
					if(isEmpty===false)
						this.uploadLock(true);
				}else if(event.target.closest("div.sbImageContainer > div.imgPreview > div.deleteImage")){
					if(this.lock===true) return;
					let index = event.target.closest("div.sbImageContainer > div.imgPreview > div.deleteImage").getAttribute("data-index");
					this.deleteImage(index);
				}
			});
		});
	}
}