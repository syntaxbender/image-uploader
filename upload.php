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
	/*
	<div class="resimsil">
		<img src="./assets/img/cancel.svg">
	</div>
	*/
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
	/*'<div class="uploadbasarili">\
		<img src="./assets/img/done.svg">\
	</div>\
	<div class="resimsil">\
		<img src="./assets/img/cancel.svg">\
	</div>';*/
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
	var r = new XMLHttpRequest();
	r.open(method, url, true);
	r.upload.addEventListener("progress", function(evt){
		if (evt.lengthComputable){
			let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[num];

			var percentComplete = ((evt.loaded / evt.total) * 100);
			imgdiv.innerHTML = "<div class=\"uploadPercentage\"><img class=\"circle-img\" src=\"./assets/img/circle.svg\">"+Math.round(percentComplete)+"%</div>";
		}
	}, false);
	r.onreadystatechange = function () {
		if (this.readyState == 4){
			if(this.status == 200){
				uploadDone(true,num,this.responseText);
			}else{
				uploadDone(false,num,"hata");
			} 
		} 
	};
	r.send(parameters);
}
function uploadDone(status, index, data){
	var data = JSON.parse(data);
	if(status === true && data[0] === true){
		uploads[index] = data[1];
	}
	current_uploads.splice(current_uploads.indexOf(index), 1);
	let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[index];
	imgdiv.innerHTML = uploadDoneContainer(index).innerHTML;
	//$(".resimyukle:nth-of-type("+(index+1)+")").html(uploadDoneContainerx.innerHTML);

	if(current_uploads.length<1){
		lock=false;
		let uploadbutton = document.querySelector("div.sbImageContainer > div.uploadButtonBox");
		uploadbutton.removeAttribute("style");
		//uploadbutton.setAttribute("style", "background:#848484; color:rgb(204 204 204);");
		uploadbutton.querySelector("span").innerHTML = "Yükle";
		let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview");

		for(let i=0; i<imgdiv.length; i++){
			let currentinput = imgdiv[i].querySelector("input"); 
			if(currentinput != null) currentinput.setAttribute("disabled", false);
		}
		//$("div.resimyolla").css("border-color","#34b100");
		//$("div.resimyolla").css("color","#34b100");
		//$("div.resimyukle input").prop("disabled", false);
	}
}
function showimage(element,data,filetype){
	let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview");
	imgdiv[element].innerHTML = uploadImageChoosedContainer(element).innerHTML;
	let blob = new Blob([data], {type: filetype});
	imgdiv[element].style.background = "url(\""+URL.createObjectURL(blob)+"\") no-repeat center center";
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
		if(lock===true) return;
		if (event.target.files && event.target.files[0]) {

			//let reader = new FileReader();
			//reader.readAsDataURL(event.target.files[0]);
			let img_index = event.target.getAttribute("data-index");
			let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[img_index];
			var file = event.target.files[0];
			if(file.type == "image/gif" || file.type == "image/jpeg" || file.type == "image/png" || file.type == "image/bmp"){
				resimler[img_index] = file;
				showimage(img_index,file,file.type);

				//reader.onload = function(e){
				//}
			}else{
				alert("Yalnızca jpeg, gif, bmp ve png türüne sahip dosyalar yükleyebilirsiniz.");
			}
		}
	});
	window.addEventListener('click', event => {
		if(event.target.closest(".uploadButtonBox")){
			if(lock === true) return;
			var isEmpty = true;
			for(var i=0;i<resimler.length;i++){
				if(resimler != null && resimler[i] != null){
					//console.log(resimler[i]);
					let formData = new FormData();
					formData.append('image', resimler[i]);
					current_uploads.push(i);
					uploadImage("POST", "./upload.php",formData, true, i);
					isEmpty=false;
				}
			}
			if(isEmpty===false){
				lock = true;
				// uploadButtonBox
				let uploadbutton = document.querySelector("div.sbImageContainer > div.uploadButtonBox");
				uploadbutton.setAttribute("style", "background:#848484; color:rgb(204 204 204);");
				uploadbutton.querySelector("span").innerHTML = "...";
				//uploadbutton.style.borderColor = "#aba1a1";
				//uploadbutton.removeAttribute("style");
				let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview");
				for(let i=0; i<imgdiv.length; i++){
					let currentinput = imgdiv[i].querySelector("input"); 
					if(currentinput != null) currentinput.setAttribute("disabled", true);
					console.log(imgdiv[i]);
					console.log(imgdiv[i].querySelector("input"));
				}
				//$("div.resimyukle input").prop("disabled", true);
			}
		}else if(event.target.closest(".deleteImage")){
			if(lock===true) return;
			let img_indexx = event.target.closest(".deleteImage").getAttribute("data-index");

			//var img_index = $(this).parent().index()-1;
			delete resimler[img_indexx];
			if(uploads != null && uploads[img_indexx] != null) delete uploads[img_indexx];
			let imgdiv = document.querySelectorAll("div.sbImageContainer > div.imgPreview")[img_indexx];
			imgdiv.removeAttribute("style");
			imgdiv.innerHTML = uploadDefContainer(img_indexx).innerHTML;
			//$(this).parent().html(uploadDefContainer.innerHTML);
		}else if(event.target.closest("div.sbImageContainer > div.imgPreview input")){

		}
	});
	// uploads değişkeninde yüklenen fotiler saklanıyor..
	// register fonku ekle uploaddone fonku içinde çağırılsın.
});
</script>