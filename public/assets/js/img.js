/*Pour la premiere image*/
		var lien = document.getElementById('lienImage');
		var fileUpload = document.getElementById('form_image_1');	
			



		fileUpload.addEventListener('change',getImage,false);

			function getImage(){
			var uploadImage = document.getElementById('placeImage');
			
			var newDiv = document.createElement('div');
			newDiv.id = 'placeImage';

			  console.log("images",this.files[0]);
			  var imageToProcess = this.files[0];

			  let newImage = new Image(imageToProcess.width , imageToProcess.height);
			  newImage.src = URL.createObjectURL(imageToProcess);
			  newImage.width = "300";
			  newImage.height = "300";
			  newImage.id = "image"
			  newImage.className = "card-img img-fluid";


			  newDiv.appendChild(newImage);
			  lien.replaceChild(newDiv,uploadImage);
			}

/*Fin du code*/
/*Pour les autres images*/
var lien_2 = document.getElementById('lienImage_2');
		var fileUpload_2 = document.getElementById('form_image_2');	
			



		fileUpload_2.addEventListener('change',getImage_2,false);

			function getImage_2(){
			var uploadImage_2 = document.getElementById('placeImage_2');
			
			var newDiv = document.createElement('div');
			newDiv.id = 'placeImage_2';

			  console.log("images",this.files[0]);
			  var imageToProcess = this.files[0];

			  let newImage = new Image(imageToProcess.width , imageToProcess.height);
			  newImage.src = URL.createObjectURL(imageToProcess);
			  newImage.width = "300";
			  newImage.height = "300";
			  newImage.id = "image"
			  newImage.className = "card-img img-fluid";


			  newDiv.appendChild(newImage);
			  lien_2.replaceChild(newDiv,uploadImage_2);
			}

var lien_3 = document.getElementById('lienImage_3');
		var fileUpload_3 = document.getElementById('form_image_3');	
			



		fileUpload_3.addEventListener('change',getImage_3,false);

			function getImage_3(){
			var uploadImage_3 = document.getElementById('placeImage_3');
			
			var newDiv = document.createElement('div');
			newDiv.id = 'placeImage_3';

			  console.log("images",this.files[0]);
			  var imageToProcess = this.files[0];

			  let newImage = new Image(imageToProcess.width , imageToProcess.height);
			  newImage.src = URL.createObjectURL(imageToProcess);
			  newImage.width = "300";
			  newImage.height = "300";
			  newImage.id = "image"
			  newImage.className = "card-img img-fluid";


			  newDiv.appendChild(newImage);
			  lien_3.replaceChild(newDiv,uploadImage_3);
			}

var lien_4 = document.getElementById('lienImage_4');
		var fileUpload_4 = document.getElementById('form_image_4');	
			



		fileUpload_4.addEventListener('change',getImage_4,false);

			function getImage_4(){
			var uploadImage_4 = document.getElementById('placeImage_4');
			
			var newDiv = document.createElement('div');
			newDiv.id = 'placeImage_4';

			  console.log("images",this.files[0]);
			  var imageToProcess = this.files[0];

			  let newImage = new Image(imageToProcess.width , imageToProcess.height);
			  newImage.src = URL.createObjectURL(imageToProcess);
			  newImage.width = "300";
			  newImage.height = "300";
			  newImage.id = "image"
			  newImage.className = "card-img img-fluid";


			  newDiv.appendChild(newImage);
			  lien_4.replaceChild(newDiv,uploadImage_4);
			}
