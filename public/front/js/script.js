/************************** COUNTDOWN **************************/

const days = document.querySelectorAll('days');
const hours = document.querySelectorAll('hours');
const minutes = document.querySelectorAll('minutes');
const seconds = document.querySelectorAll('seconds');


const newYearTime =  new Date('October 22,2022 10:30:00');

function updateCountDownTime(){
    const currentTime = new Date();
    const diff = newYearTime - currentTime;
    const d = Math.floor(diff/1000/60/60/24);
    const h = Math.floor(diff/1000/60/60)%24;
    const m = Math.floor(diff/1000/60)%60;
    const s = Math.floor(diff/1000)%60;

    document.getElementById('days').innerHTML = d;
    document.getElementById('hours').innerHTML = h < 10 ? '0' + h : h ;
    document.getElementById('minutes').innerHTML = m < 10 ? '0' + m : m ;
    document.getElementById('seconds').innerHTML = s < 10 ? '0' + s : s ;
}

setInterval(updateCountDownTime, 1000);

/************************** END COUNTDOWN **************************/


const menuHamburger = document.querySelector("#menu-hamburger");
const navLinks = document.querySelector(".menu-right");
menuHamburger.addEventListener('click',()=>{
        navLinks.classList.toggle('mobile-menu')
        });

/************************************* MUSIC ************************/
const mysong = document.getElementById('soundtrack');
const musicIcon = document.getElementById('music-control');
musicIcon.onclick = function(){
    if(mysong.paused){
        mysong.play();
        musicIcon.style.backgroundColor = "white";
    }else{
        mysong.pause();
        musicIcon.style.backgroundColor = "";
    }
}


