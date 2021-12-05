 function onClickBtnLike(event){
                event.preventDefault();
                const url = this.href;
                var span = this.querySelector('span.js-message');


                axios.get(url).then(function(response){
                	//alert(response.data.message);
                    	span.textContent = response.data.message;
                        span.classList.replace('badge-success','badge-info');
                });

}
document.querySelectorAll('a.js-message').forEach(function (link){
        link.addEventListener('click',onClickBtnLike);
})