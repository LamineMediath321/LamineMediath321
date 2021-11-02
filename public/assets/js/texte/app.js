const textAnim = document.getElementById('h1Text');

//console.log(new typewriter(textAnim));

new Typewriter(textAnim , {
  loop: true,
  deleteSpeed: 20
})
.changeDelay(60)
.typeString('Sur <span style="color :green;">Ladiastore</span>  ')
.pauseFor(300)
.typeString('<strong>, Vendez </strong> ')
.pause(1000)
.deleteChars(8)
.typeString('<strong> Achetez</strong> ')
.typeString('<strong> beaucoup plus librement</strong> !')
.pauseFor(1000)
.start()
