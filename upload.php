<?php
if($_FILES){
echo json_encode([true,"asdasd.jpg"]);
exit();
}
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<link rel="stylesheet" href="./assets/css/style.css" />

<div class="sbImageContainer">

</div>
<script>
var resimler = [];
var current_uploads = [];
var uploads = [];
var lock = false;

function uploadStartButton(text){
	let uploadStartIcon = document.createElement("img");
	uploadStartIcon.setAttribute("src","./assets/img/upload.svg");
	let uploadStartTextSpan = document.createElement("span");
	uploadStartTextSpan.innerHTML = text;
	let uploadButtonBox = document.createElement("div");
	uploadButtonBox.setAttribute("class","uploadButtonBox");
	uploadButtonBox.appendChild(uploadStartIcon);
	uploadButtonBox.appendChild(uploadStartTextSpan);
	return uploadButtonBox;
}

function uploadDefContainer(index){
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
}
function uploadImageChoosedContainer(index){
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
}
function uploadDoneContainer(index){
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
}
function uploadImage(method, url, parameters = {}, isSync,num){
	var xhr = new XMLHttpRequest();
	xhr.open(method, url, true);
	xhr.onreadystatechange = function () {
		if (this.readyState == 4){
			if(this.status == 200){
				uploadDone(true,num,this.responseText);
			}else{
				uploadDone(false,num,"hata");
			} 
		} 
	};
	xhr.send(parameters);
	xhr.upload.addEventListener("progress", function(evt){
		if (evt.lengthComputable){
			let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[num];
			var percentComplete = ((evt.loaded / evt.total) * 100);
			imgdiv.innerHTML = "<div class=\"uploadPercentage\"><img class=\"circle-img\" src=\"./assets/img/circle.svg\">"+Math.round(percentComplete)+"%</div>";
		}
	}, false);
}
function uploadDone(status, index, data){
	var data = JSON.parse(data);
	if(status === true && data[0] === true){
		uploads[index] = data[1];
	}
	current_uploads.splice(current_uploads.indexOf(index), 1);
	let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[index];
	imgdiv.innerHTML = uploadDoneContainer(index).innerHTML;
	if(current_uploads.length<1){
		lock=false;
		let uploadbutton = document.querySelector("div.sbImageContainer > div.uploadButtonBox");
		uploadbutton.removeAttribute("style");
		uploadbutton.querySelector("span").innerHTML = "Yükle";
		let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview");

		for(let i=0; i<imgdiv.length; i++){
			let currentinput = imgdiv[i].querySelector("input"); 
			if(currentinput != null) currentinput.removeAttribute("disabled");
		}
	}
}
function showimage(element,data,filetype){
	let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview");
	imgdiv[element].innerHTML = uploadImageChoosedContainer(element).innerHTML;
	let blob = new Blob([data], {type: filetype});
	imgdiv[element].setAttribute("style","background:url(\""+URL.createObjectURL(blob)+"\") no-repeat center center;")
	//imgdiv[element].style.background = "";
	var client_files = 0;
	for(i in resimler){
		if(resimler[i] != null) client_files = client_files + 1;
	}
	if(client_files==imgdiv.length){
		if(client_files == 10) return;
		imgdiv[imgdiv.length-1].insertAdjacentHTML("afterend",uploadDefContainer(imgdiv.length).outerHTML);
	}
}
function generateContainer(num,text){
	let buffer = "";
	for (let i = 0; i < num; i++){
		buffer += uploadDefContainer(i).outerHTML;
	}
	buffer += uploadStartButton(text).outerHTML;
	let sbimage = document.querySelector(".sbImageContainer").innerHTML = buffer;
}
window.addEventListener('load', event => {
	generateContainer(5,"Yükle");
	window.addEventListener('change', event => {
		if(event.target.closest("div.sbImageContainer > div.imgPreview input")){
			let input = event.target.closest("div.sbImageContainer > div.imgPreview input");
			if(lock===true) return;
			if (input.files && input.files[0]){
				const index = input.getAttribute("data-index");
				let file = input.files[0];
				if(file.type == "image/gif" || file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/bmp"){
					resimler[index] = file;
					showimage(index,file,file.type);
				}else{
					alert("Yalnızca jpeg, gif, bmp ve png türüne sahip dosyalar yükleyebilirsiniz.");
				}
			}
		}
	});
	window.addEventListener('click', event => {
		if(event.target.closest(".uploadButtonBox")){
			if(lock === true) return;
			var isEmpty = true;
			for(let i in resimler){
				if(resimler != null && resimler[i] != null){
					let formData = new FormData();
					formData.append('image', resimler[i]);
					current_uploads.push(i);
					uploadImage("POST", "./upload.php",formData, true, i);
					isEmpty=false;
				}
			}
			if(isEmpty===false){
				lock = true;
				let uploadbutton = document.querySelector("div.sbImageContainer > div.uploadButtonBox");
				uploadbutton.setAttribute("style", "background:#848484; color:rgb(204 204 204);");
				uploadbutton.querySelector("span").innerHTML = "...";
				let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview");
				for(let i=0; i<imgdiv.length; i++){
					let currentinput = imgdiv[i].querySelector("input"); 
					if(currentinput != null) currentinput.setAttribute("disabled", true);
					console.log(imgdiv[i]);
					console.log(imgdiv[i].querySelector("input"));
				}
			}
		}else if(event.target.closest(".deleteImage")){
			if(lock===true) return;
			let index = event.target.closest(".deleteImage").getAttribute("data-index");
			resimler.splice(index, 1);

			//delete resimler[index];
			if(uploads != null && uploads[index] != null) delete uploads[index];
			let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[index];
			imgdiv.removeAttribute("style");
			imgdiv.innerHTML = uploadDefContainer(index).innerHTML;
		}
	});
	// uploads değişkeninde yüklenen fotiler saklanıyor..
	// register fonku ekle uploaddone fonku içinde çağırılsın.
});
</script>