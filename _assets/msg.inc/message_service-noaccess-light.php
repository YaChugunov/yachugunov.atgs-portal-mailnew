<style>
	
	
.wave-divider {
    width: 100%;
    height: 60px;
    display:block;    
}
.wave-parallax1 {
    animation: wave-move1 10s linear infinite;
}
.wave-parallax2 {
    animation: wave-move2 8s linear infinite;
}
.wave-parallax3 {
    animation: wave-move3 6s linear infinite;
}
.wave-parallax4 {
    animation: wave-move4 4s linear infinite;
}
@keyframes wave-move1 {
    0% {
        transform: translateX(85px);
    }
    100% {
        transform: translateX(-90px);
    }
}
@keyframes wave-move2 {
    0% {
        transform: translateX(-90px);
    }
    100% {
        transform: translateX(85px);
    }
}
@keyframes wave-move3 {
    0% {
        transform: translateX(85px);
    }
    100% {
        transform: translateX(-90px);
    }
}
@keyframes wave-move4 {
    0% {
        transform: translateX(-90px);
    }
    100% {
        transform: translateX(85px);
    }
}
.dark-block {
    background: #337AB7;
    padding: 50px 0;
    font-size: 26px;
    font-family: 'Roboto Condensed', sans-serif;
    text-align: center;
    color: #FFF;
}
.dark-bg {
    background: #337AB7;
}
.light-block {
    background: #FFF;
    padding: 50px 0;
    font-size: 26px;
    font-family: 'Roboto Condensed', sans-serif;
    text-align: center;
    color: #337AB7;
}


</style>




<div class="light-block"></div>
<svg class="wave-divider" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none">
    <defs>
        <path id="gentle-wave"
        d="M-160 44c30 0 
        58-18 88-18s
        58 18 88 18 
        58-18 88-18 
        58 18 88 18
        v44h-352z" />
    </defs>
    <g class="wave-parallax1"><use xlink:href="#gentle-wave" x="50" y="3" fill="#BFE2FF"/></g>
    <g class="wave-parallax2"><use xlink:href="#gentle-wave" x="50" y="0" fill="#5e9cd1"/></g>
    <g class="wave-parallax3"><use xlink:href="#gentle-wave" x="50" y="9" fill="#73bbf5"/></g>
    <g class="wave-parallax4"><use xlink:href="#gentle-wave" x="50" y="6" fill="#337AB7"/></g>
</svg>
<div class="dark-block">Я работаю, не подглядывайте :)</div>
<svg class="wave-divider dark-bg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none">
    <g class="wave-parallax1"><use xlink:href="#gentle-wave" x="50" y="3" fill="#5e9cd1"/></g>
    <g class="wave-parallax2"><use xlink:href="#gentle-wave" x="50" y="0" fill="#73bbf5"/></g>
    <g class="wave-parallax3"><use xlink:href="#gentle-wave" x="50" y="9" fill="#BFE2FF"/></g>
    <g class="wave-parallax4"><use xlink:href="#gentle-wave" x="50" y="6" fill="#FFFFFF"/></g>
</svg>
<div class="light-block"></div>