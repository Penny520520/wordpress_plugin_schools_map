// note sch_line-height < 500px,  
// x  & y <600px
// img size <600px

var counName_Arr = [];
var counImg_Arr = [];
var btnId_Arr = [];
// more button and more contents under the responsond button
coun_data.forEach(function(country, index, array){
    let sm3Nav = document.getElementsByClassName('sm3-nav')[0];

    let nCounBtn = document.createElement('button');
    nCounBtn.className = 'nav-btn';
    nCounBtn.textContent = country["coun_name"];


    sm3Nav.appendChild(nCounBtn);
    //sm3Nav2.appendChild(nCounBtn2);

    let newCounName = country["coun_name"];
    let newCounImg = country["coun_img"];
    let newBtnId = country["coun_btnid"];
    counName_Arr.push(newCounName);
    counImg_Arr.push(newCounImg);
    btnId_Arr.push(newBtnId);

});
//console.log(counName_Arr);


// const data
var navBtns = document.getElementsByClassName('nav-btn');
var counName = document.querySelector('.country-name');
var counImg = document.querySelector('.country-img');
var allSchBtn = document.querySelector('.allSchBtn');
var counNameBtn = document.querySelector('.counNameBtn');

// default tab
navBtns[0].classList.add("btn-active");
tabControl(0);

//this value
for(let i=0; i < coun_data.length; i++) {

    navBtns[i].setAttribute('index',i);

    navBtns[i].onclick = function(){

        for(let i=0; i < navBtns.length; i++) {
            navBtns[i].classList.remove("btn-active");
        }

        this.classList.add("btn-active");

        tabControl(this.getAttribute('index'));
        
    };

};

// thumbnail image control
var all_thuimg = document.getElementsByClassName('thuimg');
for(let i=0; i < all_thuimg.length; i++) {

    all_thuimg[i].setAttribute('index',i);

    all_thuimg[i].onclick = function(){

        var curr_thuimg = all_thuimg[this.getAttribute('index')].src;
        document.querySelector('.school-largeimg').src =  curr_thuimg;

        for(let i=0; i < all_thuimg.length; i++) {
            all_thuimg[i].style.border = "2px solid white";
        }
        this.style.border = "2px solid black";
    };

};

// click bookmark to get the responding school intro
var schLogo = document.getElementsByClassName('sch-logo');
for(let i=0; i < schLogo.length; i++){
    
    schLogo[i].addEventListener('click',function(e){
        
        schoolLogoimgClick(e);
    });
};

// $('.sch-logo').click(function(e) {
//     schoolLogoimgClick(e);
// });

// ****************public function **********************
function tabControl(index){

    counName.innerHTML = counName_Arr[index];
    counImg.src = counImg_Arr[index];
    allSchBtn.id = btnId_Arr[index];
    counNameBtn.innerHTML = counName_Arr[index];

    // image & line
    var all_logoimg_size_Arr = [];
    coun_sch_data[index].forEach(function(coun_sch, schnum, array){
        document.getElementsByClassName('sch-logo')[schnum].src = coun_sch['sch_logoimg'];
        document.getElementsByClassName('sch-logo')[schnum].style.width = coun_sch['sch_logoimg_size'] + 'px';
        document.getElementsByClassName('line')[schnum].style.height = coun_sch['sch_linh']+ 'px';

        // transform X Y
        document.getElementsByClassName('bookmark')[schnum].style.transform =  'translate(' + coun_sch['sch_tranx'] + 'px' + ',' + coun_sch['sch_trany'] + 'px' + ')';

        all_logoimg_size_Arr.push( Number(coun_sch['sch_logoimg_size']));
    });

    //console.log(all_logoimg_size_Arr);
    var indexOfMaxValue = all_logoimg_size_Arr.reduce((iMax, x, i, arr) => x > all_logoimg_size_Arr[iMax] ? i : iMax, 0);
    //console.log(indexOfMaxValue);

    document.querySelector('.sch-logo-heading').src = coun_sch_data[index][indexOfMaxValue]['sch_logoimg'];

    document.querySelector('.sch-name').textContent = coun_sch_data[index][indexOfMaxValue]['sch_name'];
    document.querySelector('.sch-des').textContent = coun_sch_data[index][indexOfMaxValue]['sch_des']; 

    document.querySelector('.school-largeimg').src = coun_sch_data[index][indexOfMaxValue]['sch_bimg']; 
    
    document.querySelector('.school-largeimg').src = coun_sch_data[index][indexOfMaxValue]['sch_bimg']; 
    document.querySelector('.thuimgL').src = coun_sch_data[index][indexOfMaxValue]['sch_bimg']; 
    document.querySelector('.thuimg1').src = coun_sch_data[index][indexOfMaxValue]['sch_thimg1']; 
    document.querySelector('.thuimg2').src = coun_sch_data[index][indexOfMaxValue]['sch_thimg2']; 

}

function schoolLogoimgClick(e){
    
    // var schLogo = document.getElementsByClassName('sch-logo');
    for(let i=0; i < schLogo.length; i++) {
        schLogo[i].classList.remove("large-size");
        schLogo[i].classList.add("regular-size");

        document.getElementsByClassName('bookmark')[i].style.zIndex = 0;
    }
    e.target.classList.add("large-size");


    if(e.target.parentNode) {

        e.target.parentNode.style.zIndex = 100;

    }

    // Right column change as well
    var actBtn = document.querySelector('.btn-active');
    var indexBtn = actBtn.getAttribute('index');
    //console.log(indexBtn);

    var curr_all_logoimg_size_Arr = [];
    for(let i=0; i < schLogo.length; i++){
        var new_logoimg_h = document.getElementsByClassName('sch-logo')[i].offsetHeight;

        curr_all_logoimg_size_Arr.push(new_logoimg_h);

    }

    //console.log(curr_all_logoimg_size_Arr);
    let cindexOfMaxValue = curr_all_logoimg_size_Arr.reduce((iMax, x, i, arr) => x > curr_all_logoimg_size_Arr[iMax] ? i : iMax, 0);
    //console.log(cindexOfMaxValue);

    document.querySelector('.sch-logo-heading').src = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_logoimg'];

    document.querySelector('.sch-name').textContent = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_name'];
    document.querySelector('.sch-des').textContent = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_des']; 

    document.querySelector('.school-largeimg').src = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_bimg']; 
    
    document.querySelector('.school-largeimg').src = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_bimg']; 
    document.querySelector('.thuimgL').src = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_bimg']; 
    document.querySelector('.thuimg1').src = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_thimg1']; 
    document.querySelector('.thuimg2').src = coun_sch_data[indexBtn][cindexOfMaxValue]['sch_thimg2']; 

}



