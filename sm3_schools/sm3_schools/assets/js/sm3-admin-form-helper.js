// team_data will be the transformed version of the JSON stored in the options
var coun_data = [];
var mediaUploader;

// jquery is already enqueued as a dependency
jQuery(document).ready(function($){
    
    /****************** Team form handlers ******************/ 

    // check if team_data_json is undefined (ie it only loads in the team view)    
    if(typeof coun_data_json !== 'undefined' && coun_data_json !== ''){
        coun_data = JSON.parse(coun_data_json);
        //console.log(coun_data_json);
        //console.log(coun_data);
    }
    
    // The add button click handler
    $('.btn-add-coun').click(function(){
        addCoun(this);                       
    });

    // the delete button click handler
    $('.btn-del-coun').click(function(){        
        delCoun(this);
    });

    $('.btn-edit-schools').click(function(){
        editSchools(this);
    });


    $('.media-picker-button').click(function(e) {
        mediaButtonClick(e);
    });

    /****************** schools form  ******************/ 

    $('.btn-cancel-schools').click(function(){
        // cancel members and return to first form
        console.log('t1')
        window.location.href = "options-general.php?page=sm3_schools";
    });
});

/****************** processing functions ******************/ 
/* these processes are broken out because click handlers need to be added 
   after the fact for dynamically created content, it also helps to keep code clean */
function addCoun(addButton){

    // the row is two parents back
    let cRow = addButton.parentNode.parentNode;
    let cCodeField = document.querySelector('#new_councode');
    let cNameField = document.querySelector('#new_counname');
    let cCounImgField = document.querySelector('#new_counimg');
    let cCounbtnIdField = document.querySelector('#new_counbtnid');
    //console.log(cCounbtnIdField);
    if(getCounIndex(cCodeField.value) === -1){
        // code doesn't exist in current list of teams
        // first add the team to the array
        let newCoun = {
            coun_code: cCodeField.value.trim().toLowerCase(), 
            coun_name: cNameField.value.trim(), 
            coun_img: cCounImgField.value.trim(),
            coun_btnid: cCounbtnIdField.value.trim(),
        };

        coun_data.push(newCoun);
        console.log(coun_data);
        // insert a row with these values after the last row of existing data
        cRow.parentNode.insertBefore(returnCounRow(newCoun), cRow);

        // this stores added teams so that when the form is updated associated member options are created
        let hiddenAddField = document.querySelector('#last_coun_added');
        if(hiddenAddField.value !== ''){
            hiddenAddField.value += ';' + newCoun.coun_code;
        }
        else{
            hiddenAddField.value = newCoun.coun_code;
        }       
        
        // clear the input fields
        cCodeField.value = '';
        cNameField.value = '';
        cCounImgField.value = '';
        cCounbtnIdField.value = '';
    }
    else{
        // optionally give some sort of message here
        console.log('not added, code already exists for this coun');
    } 
}

function delCoun(delButton){
    // the row is two parents back
    let cRow = delButton.parentNode.parentNode;
    let delCoun = cRow.querySelector('.coun-name-field').value.trim();        
    
    let cCounIndex = getCounIndex(delCoun);

    // remove from array
    coun_data.splice(cCounIndex, 1);

    // remove row
    cRow.remove();

    // this stores deleted teams so that when the form is updated associated member options are created
    let hiddenDelField = document.querySelector('#last_coun_deleted');

    if(hiddenDelField.value !== ''){
        document.querySelector('#last_coun_deleted').value += ';' + delCoun;
    }
    else{
        document.querySelector('#last_coun_deleted').value = delCoun;
    }
}

function editSchools(editButton){
    // the row is two parents back
    let cRow = editButton.parentNode.parentNode;
    let cCodeField = cRow.querySelector('.coun-code-field');
    let cNameField = cRow.querySelector('.coun-name-field');      

    console.log('edit');

    // use query params to load member version
    window.location.href = "options-general.php?page=sm3_schools&mode=schooledit&councode=" + cCodeField.value + "&counname=" + cNameField.value;
}



/****************** utility function ******************/ 
function returnCounRow(newCoun){

    let nRow = document.createElement('tr');
    let nCodeCell = document.createElement('td');
    let nNameCell = document.createElement('td');
    let nCounImgCell = document.createElement('td');
    let nCounBtnIdCell = document.createElement('td');
    let nActionCell = document.createElement('td');

    // create the name input box and append to the cell
    let nCodeInput = document.createElement('input');
    nCodeInput.name = 'councode_' + newCoun.coun_code;
    nCodeInput.id = 'councode_' + newCoun.coun_code;
    nCodeInput.classList = 'regular-text coun-code-field'
    nCodeInput.maxLength = 3;
    nCodeInput.value = newCoun.coun_code;
    nCodeCell.appendChild(nCodeInput);
    
    // create the name input box and append to the cell
    let nCounInput = document.createElement('input');
    nCounInput.name = 'counname_' + newCoun.coun_name;
    nCounInput.id = 'counname_' + newCoun.coun_name;
    nCounInput.classList = 'regular-text coun-name-field'
    nCounInput.maxLength = 20;
    nCounInput.value = newCoun.coun_name;
    nNameCell.appendChild(nCounInput);

    // the name input box (uses the same code as a suffix) and append to the cell
    let nCounImgInput = document.createElement('input');
    nCounImgInput.name = 'counimg_' + newCoun.coun_name;
    nCounImgInput.id = 'counimg_' + newCoun.coun_name;
    nCounImgInput.classList = 'regular-text coun-path-field'; 
    nCounImgCell.appendChild(nCounImgInput);
  
    let nCounPathBtn = document.createElement('input');
    nCounPathBtn.id = 'upload_image_button_' + newCoun.coun_name;
    nCounPathBtn.setAttribute('data-del-index', 'counimg_' + newCoun.coun_name);
    nCounPathBtn.type = 'button';
    nCounPathBtn.classList = 'button-primary media-picker-button';
    nCounPathBtn.value = 'Select';
    nCounPathBtn.addEventListener('click', function(event){mediaButtonClick(event)});

    // nPathCell.className = 'is-flex';
    nCounImgCell.appendChild(nCounImgInput);
    nCounImgCell.appendChild(nCounPathBtn);

    // create the btn id input box and append to the cell
    let nCounBtnIdInput = document.createElement('input');
    nCounBtnIdInput.name = 'counbtnid_' + newCoun.coun_name;
    nCounBtnIdInput.id = 'counbtnid_' + newCoun.coun_name;
    nCounBtnIdInput.classList = 'regular-text coun-btnid-field'
    nCounBtnIdInput.maxLength = 20;
    nCounBtnIdInput.value = newCoun.coun_btnid;
    nCounBtnIdCell.appendChild(nCounBtnIdInput);

    // the delete button
    let nDelButton = document.createElement('button');
    nDelButton.type = 'button';
    nDelButton.classList = 'button button-secondary btn-del-coun';
    nDelButton.setAttribute('data-tcode', newCoun.coun_name);
    nDelButton.addEventListener('click', function(event){delCoun(event.target)});
    nDelButton.textContent = 'Delete Country';

    nActionCell.appendChild(nDelButton);

    nRow.appendChild(nCodeCell);
    nRow.appendChild(nNameCell);
    nRow.appendChild(nCounImgCell);
    nRow.appendChild(nCounBtnIdCell);
    nRow.appendChild(nActionCell);
    
    return nRow;

}

function getCounIndex(chkCode){

    // this will return the index of the array member that has a matching code
    // if no code is matched then it will return -1
    // console.log(newCoun);
    return coun_data.findIndex(({coun_code}) => coun_code === chkCode);
}

function mediaButtonClick(e){
    console.log('media-click');
   
    e.preventDefault();      
  
    var targetBox = document.querySelector('#' + e.target.getAttribute('data-target-textbox'));

    console.log('targetbox:' + targetBox.id);

    // open the media picker ui    
    if (mediaUploader) {
        mediaUploader = null;        
    }

    mediaUploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Image',
        button: {
        text: 'Choose Image'
    }, multiple: false });

    // when an image is selected, fire it back to the textbox field
    mediaUploader.on('select', function() {
        var attachment = mediaUploader.state().get('selection').first().toJSON();      
        console.log('t:' + targetBox.id);
        targetBox.value = attachment.url;
    });

    mediaUploader.open();
}




