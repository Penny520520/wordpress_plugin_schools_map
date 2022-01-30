<?php
/**
 * @package sm3_schools
 * @version 1.0.0
 */
/*
Plugin Name: SM3 - Schools
Plugin URI: 
Description: a collection of schools data in the admin ui
Author: Penny
Version: 1.0.0
Author URI: 
*/

if ( ! class_exists( 'Sm3_Schools' ) ) {
    
    class Sm3_Schools {
    
        // constructor
        public function __construct() {

            if(is_admin()){

                // hook for the admin settings 
                add_action( 'admin_menu', array( $this, 'create_plug_settings_page' ) );
                
            }
            else{
                // public hooks to go here
                add_action( 'wp_enqueue_scripts', array($this, 'enqueue_public_files'), 1);    
                
                add_shortcode('sm3_schools', array($this, 'get_schools_shortcode'));

                add_shortcode('sm3_test', array($this, 'get_test_shortcode'));			
            }	

        }

        // ******************************  PUBLIC METHODS ******************************
        function enqueue_public_files(){


            // enqueue the css for the public page
            wp_enqueue_style( 'sm3-schools', plugins_url('assets/css/sm3-style.css' , __FILE__ ), array(), '1.0.0' );	
            
            wp_enqueue_script('assets-sm3-jquery', get_stylesheet_directory_uri().'/assets/js/jquery-3.5.1.slim.min.js' , null, '3.5.1', true);

            // enqueue the js for the public page   *'assets-jquery'  jquery-ui-core
            wp_enqueue_script( 'sm3-schools', plugins_url('assets/js/sm3-common.js' , __FILE__ ), array('assets-sm3-jquery'), '1.0.0', true );	

                    
        }      

		function get_schools_shortcode($atts, $content=null){  
            echo "<!-- v1.4 -->";
            $coun_data = json_decode(get_option('sm3_coun_data'));
            //var_dump($coun_data);

            echo '<script>';
            echo 'var coun_data = ' . get_option('sm3_coun_data') . ';';
            echo 'var coun_sch_data = [';
            foreach($coun_data as $curr_coun){
                //echo '<div>test('.$iter.'): '.$curr_coun->coun_code .'</div>';
                
                $curr_coun_sch_data = get_option('sm3_school_data_'.$curr_coun->coun_code);
                
                // var_dump($curr_coun_sch_data);
                // only add if there's something there
                if($curr_coun_sch_data){
                    echo $curr_coun_sch_data. ','; 
                }
            }

            // this is just a test to show that you can see the canada data after the fact
            echo '];';
            echo '</script>';
            

            // use output buffer
            ob_start();

            ?>

            <div class="sm3-section-container">
                <!-- button to switch each country -->
                <div class="sm3-nav"></div>
                <h2>Study in <span class="country-name"></span></h2>
                <div class="schoolmap-container is-flex">
                    <!-- left school map -->
                    <div class="left-column">
                        <img src="" alt="" class="country-img">
                        <!-- bookmarks -->
                        <div class="first-bookmark bookmark">
                            <img class="sch-logo" alt="">
                            <div class="line"></div>
                        </div>
                        <div class="second-bookmark bookmark">
                            <img class="sch-logo" alt="">
                            <div class="line"></div>
                        </div>
                        <div class="third-bookmark bookmark">
                            <img class="sch-logo" alt="">
                            <div class="line"></div>
                        </div>
                        <div class="fourth-bookmark bookmark">
                            <img class="sch-logo" alt="">
                            <div class="line"></div>
                        </div>
                        <div class="fifth-bookmark bookmark">
                            <img class="sch-logo" alt="">
                            <div class="line"></div>
                        </div>
                        <div class="sixth-bookmark bookmark">
                            <img class="sch-logo" alt="">
                            <div class="line"></div>
                        </div>
                        <a class="allSchBtn" id="">View All <span class="counNameBtn"></span> Schools
                        </a>
                    </div>
                    <!-- right about individual school brief intro -->
                    <div class="right-column">
                        <div class="heading is-flex">
                            <img src="" alt="" class="sch-logo-heading">
                            <h3 class="sch-name"></h3>
                        </div>
                        <p class="sch-des"></p>
                        <img src="" alt="" class="school-largeimg">
                        <!-- three thummails -->
                        <div class="school-thumbnails">
                            <img class="thuimgL thuimg" src="" alt="">
                            <img class="thuimg1 thuimg" src="" alt="">
                            <img  class="thuimg2 thuimg"src="" alt="">
                        </div>
                    </div>
                </div>
            </div>
            

        <?php
			// returns the output buffer and flushes it
			return ob_get_clean();		            
                    

        }


        // ******************************  ADMIN METHODS ******************************
        function enqueue_admin_files(){

            wp_enqueue_style( 'sm3-admin-style', plugins_url('assets/css/sm3-admin-style.css' , __FILE__ ), array(), '1.0.0' );

            // this will only enqueue when this plugin's admin page loads, see create_plug_settings_page() below
             wp_enqueue_script('sm3-admin-form-helper', plugins_url('assets/js/sm3-admin-form-helper.js' , __FILE__ ), array('jquery'), '1.1.0', true);  
 
             // enqueues all the js, css needed for the media selector
             wp_enqueue_media();
 
        }

        // add a page to the admin ui, in this case for the plugin settings
        public function create_plug_settings_page(){

            // creates a page in the admin ui for settings
            $page_title = 'SM3 Schools Plugin';  // the title when the plugin is selected
            $menu_title = 'SM3 Schools';  // the text for the menu
            $capability = 'manage_options';  // who can see this plugin
            $menu_slug = 'sm3_schools';   // should be unique
            $function = array( $this, 'plugin_settings_page_content' ); // function that builds the page
            $icon_url = 'dashicons-welcome-learn-more';  // can be dashicons, url of svg or none
            $position = 50; // after the standard menus
    
            // add to the admin with the params above
            $my_page = add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

            //add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_files'), 1);
            add_action('load-' . $my_page, array($this, 'load_admin_js'));
        }  

        // This function is only called when our plugin's page loads
        function load_admin_js(){
            // Unfortunately we can't just enqueue our scripts here - it's too early. So register against the proper action hook to do it
            add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_files') );
        }
    
        // this will build the form for the admin ui
        public function plugin_settings_page_content(){

            // check to see if in school edit mode
            if(isset($_GET["mode"]) && isset($_GET["councode"])){
                               
                $this->buildSchoolForm();                
            }
            else{
                $this->buildCounForm();
            }
            
        }

        public function buildCounForm(){
            if( isset($_POST['updated']) && $_POST['updated'] === 'true' ){
                $this->handleCounForm();
            }
            ?>
            <div class="wrap">
                <h2>Schools Plugin</h2>
                <form method="POST">
                    <!-- hidden fields -->
                    <input type="hidden" name="updated" value="true" />
                    <input type="hidden" id="last_coun_added" name="last_coun_added" value="" />
                    <input type="hidden" id="last_coun_deleted" name="last_coun_deleted" value="" />                    
                    <?php wp_nonce_field( 'sm3_update', 'sm3_form' ); ?>
                    <table class="form-table" id="teamTable">
                        <thead>
                            <tr>
                                <th>Country Code</th>
                                <th>Country Name</th>
                                <th>Country Map(598*506)</th>
                                <th>Button Id (for Pop Up)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <?php
                                // decode for the loop
                                $coun_data = JSON_decode(get_option('sm3_coun_data'));
                                //var_dump($coun_data);
                                
                                // only loop and build the table if the option data exists
                                if (!empty($coun_data)) {

                                    $indexCnt = 0;
                                    foreach($coun_data as $cCoun) {
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="regular-text coun-code-field" 
                                                    name="councode_<?php echo $indexCnt; ?>" id="councode_<?php echo $indexCnt; ?>" 
                                                    value="<?php echo $cCoun->coun_code; ?>" maxlength="3" />
                                            </td> 
                                            <td>
                                                <input type="text" class="regular-text coun-name-field" 
                                                    name="counname_<?php echo $indexCnt; ?>" id="counname_<?php echo $indexCnt; ?>" 
                                                    value="<?php echo $cCoun->coun_name; ?>" maxlength="20" />
                                            </td>
                                            <td>
                                                <input id="counimg_<?php echo $indexCnt; ?>" type="text" class="regular-text coun-path-field"
                                                    name="counimg_<?php echo $indexCnt; ?>"
                                                    value="<?php echo $cCoun->coun_img; ?>" readonly />
                                                    
                                                <input id="upload_image_button_<?php echo $indexCnt; ?>"
                                                    data-target-textbox="counimg_<?php echo $indexCnt; ?>"
                                                    type="button" class="button-primary media-picker-button" value="Select" />
                                            </td>                            
                                            <td>
                                                <input type="text" class="regular-text coun-btnid-field" 
                                                    name="counbtnid_<?php echo $indexCnt; ?>" id="counbtnid_<?php echo $indexCnt; ?>" 
                                                    value="<?php echo $cCoun->coun_btnid; ?>" maxlength="20"  />
                                            </td>
                                            <td>
                                                <button type="button" class="button button-secondary btn-del-coun" data-tcode="<?php echo $cCoun->coun_name ?>" >Delete Country</button>                                                
                                                <button type="button" class="button button-secondary btn-edit-schools" data-tcode="<?php echo $ccoun->coun_name ?>" >Edit Schools</button>                                                                                            
                                            </td>
                                        </tr>
                                        <?php
                                        // increment the index counter
                                        $indexCnt++;
                                    }
                                }                                
                            ?>
                            <tr>
                                <td>
                                    <input name="new_councode" id="new_councode" type="text" class="regular-text coun-code-field" 
                                    value="" placeholder="add new coun code (3 char max)" maxlength="3" />
                                </td> 
                                <td>
                                    <input name="new_counname" id="new_counname" type="text" class="regular-text coun-name-field" 
                                    value="" placeholder="add new country (20 char max)" maxlength="20" />
                                </td>                            
                                <td>
                                    <input id="new_counimg" type="text" value="" readonly />
                                        
                                    <input id="upload_image_button"
                                        data-target-textbox="coun_image"
                                        type="button" class="button-primary media-picker-button" value="Select" />
                                </td>                           
                                <td>
                                    <input name="new_counbtnid" id="new_counbtnid" type="text" class="regular-text" 
                                    value="" placeholder="add new Btn Id (20 char max)" maxlength="20" />
                                </td>
                                <td>
                                    <button type="button" class="button button-secondary btn-add-coun">Add Country</button>                                                                        
                                </td>
                            </tr> 
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Update Country List">
                                </td>
                            <tr>
                        </tfoot>
                    </table>                    

                </form>
                <script>
                    var coun_data_json = '<?php echo get_option('sm3_coun_data'); ?>';
                    console.log(coun_data_json);
                    
                </script>

                <div>
                    <h2>Note the following:</h2>
                    <ul>
                        <li>When creating a country code, you should add a three character unique code.</li>
                        <li><strong>Shortcode:</strong> <code>[sm3_schools]</code></li>
                        <li>When you <strong>Add Country</strong> or <strong>Delete Country</strong> or edit existing data, changes will not be applied until you click the <strong>Update Country List</strong> button.</li>
                        <li>For <strong>Select Image</strong>, only work after clicking Update Members </li>
                        <li>If you <strong>Edit Schools</strong> for a country without updating you will lose your changes.</li>
                        <li>You won't be able to <strong>Edit Schools</strong> for a new Country until you've updated.</li>
                        <li>You won't be able to <strong>Select</strong> for a new Country Map until after you've updated.</li>
                        <li>When creating <strong>Button Id</strong> for a new Pop Up, an unique id would be recommended (EG: can-pop).</li>
                    </ul>
                </div>
                
            </div> 
            <?php
                             
        }

        // the handler for the form
        public function handleCounForm() {
            if(
                // get and check the nonce to prevent against CSF
                ! isset( $_POST['sm3_form'] ) ||
                ! wp_verify_nonce( $_POST['sm3_form'], 'sm3_update' )
            ){ ?>
                <div class="error">
                    <p>Sorry, your nonce was not correct. Please try again.</p>
                </div> <?php
                exit;
            } else {
                
                $valid_data = true;
                
                $curr_coun_array = array();

               //var_dump($_POST["counname_0"]);
                // loop through the post fields and find the teamcodes
                foreach($_POST as $key => $val) {                   
                    
                    // check if the current field is a code field
                    if(substr($key, 0, 8) === 'councode'){                    
                        $row_index = substr($key, 9 - strlen($key));
                        //var_dump($row_index);
                        $coun_code = sanitize_text_field( $_POST[$key] );
                        $coun_name = sanitize_text_field( $_POST['counname_'.$row_index] );
                        $coun_img = sanitize_text_field( $_POST['counimg_'.$row_index] ); 
                        $coun_btnid = sanitize_text_field( $_POST['counbtnid_'.$row_index] );
                        
                        // form a team object
                        $curr_coun = array(
                            "coun_code" => $coun_code,
                            "coun_name" => $coun_name,
                            "coun_img" => $coun_img,
                            "coun_btnid" => $coun_btnid
                        );                        
                        array_push($curr_coun_array, $curr_coun);
                    }                                        
                }  
                //var_dump($curr_coun_array);             

                if($valid_data){
                    // update the entire data set for all teams
                    update_option('sm3_coun_data', json_encode($curr_coun_array));
                    $addNotes = '';
                    $delNotes = '';
                      
                    if($_POST['last_coun_added'] !== ''){
                        // the add field might have more than one code but they are separated by semi-colons
                        // get the field and break it into an array by exploding
                        $arr_new_coun_codes = explode(';', sanitize_text_field( $_POST['last_coun_added']));
                        $cntr = 0;

                        foreach($arr_new_coun_codes as &$cCode){
                            // add each member options
                            add_option('sm3_coun_schools_'.$cCode, json_encode(array()));
                            $cntr++;
                        }                       

                        $addNotes = '<p>'.$cntr.' coun(s) added</p>';
                        
                    }
                    
                    if($_POST['last_coun_deleted'] !== ''){ 

                        // get the field and break it into an array by exploding
                        $arr_del_coun_codes = explode(';', sanitize_text_field( $_POST['last_coun_deleted']));
                        $cntr = 0;

                        foreach($arr_del_coun_codes as &$cCode){
                            // add each member options
                            delete_option('sm3_coun_schools_'.$cCode, array());
                            $cntr++;
                        }                       

                        $delNotes = '<p>'.$cntr.' coun(s) removed</p>';                        
                    }
                    
                    ?><div class="updated">
                        <?php echo $addNotes; ?>
                        <?php echo $delNotes; ?>
                        <p>Other country data updated as displayed</p>
                    </div><?php
                    
                    
                } else { ?>
                    <div class="error">
                        <p>There were one or more errors with your form data.</p>
                    </div> <?php
                }
            }
        }  

        
        public function buildSchoolForm() {
            
            if( isset($_POST['updated']) && $_POST['updated'] === 'true' ){
                $this->handleSchoolForm();
            }

            // get the code from the query param
            $curr_coun_code = htmlspecialchars($_GET["councode"]);            
            
            $coun_school_data = get_option('sm3_school_data_'.$curr_coun_code);
            //$coun_school_data = get_option('sm3_school_data_'.$curr_coun_code);
            
            //var_dump($coun_school_data);
            if(!$coun_school_data){

               $coun_school_data = [];
                // create a set of schools and assign to variable
                for($i = 0; $i < 6; $i++) {                       

                        // form a team object
                        $curr_school = array(
                            "sch_num" => "",
                            "sch_name" => "",
                            "sch_tranx" => "",
                            "sch_trany" => "",
                            "sch_linh" => "",
                            "sch_logoimg" => "",   
                            "sch_logoimg_size" => "50",
                            "sch_bimg" => "",  
                            "sch_thimg1" => "",    
                            "sch_thimg2" => "", 
                            "sch_des" => "School description (recommend 20~30 chars)",
                        );               
                  
                        $curr_school = (object)$curr_school;
                        array_push($coun_school_data, $curr_school);
                } 
                //var_dump($coun_school_data);
            }
            else{
                // decode
                $coun_school_data = json_decode($coun_school_data);
            }

           //var_dump($coun_school_data);
            ?>
            <div class="wrap">
                <h2>Schools for <?php echo $_GET['counname'] ?></h2>
                <form method="POST">
                    <input type="hidden" name="curr_coun_code" value="<?php echo $curr_coun_code ?>" />
                    <input type="hidden" name="updated" value="true" />
                    <?php wp_nonce_field( 'sm3_update', 'sm3_form' ); ?>                                                           
                                               
                        <?php    
                            // only loop and build the table if the option data exists

                             if (!empty($coun_school_data)){

                                $indexCnt = 0;

                                foreach($coun_school_data as $cSchool) { 
                        ?>
                    <div class="tables-container">
                        <table class="form-table"> 
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>School Name</th>
                                    <th>Translate X (px)</th>
                                    <th>Translate Y (px)</th>
                                    <th>Line Height (px)</th>
                                    <th>School Logo Image(202*162)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="number" class="sch-num-field" readonly="readonly"
                                        name="sch_num_<?php echo $indexCnt; ?>" id="sch-num_<?php echo $indexCnt; ?>" 
                                        value="<?php echo $indexCnt + 1; ?>" maxlength="3" />
                                    </td>
                                    <td>                                    
                                        <input name="sch_name_<?php echo $indexCnt; ?>" id="sch_name_<?php echo $indexCnt; ?>" type="text" class="" 
                                        value="<?php echo $cSchool->sch_name; ?>" placeholder="enter the school name" maxlength="50" />
                                    </td>
                                    <td>                                    
                                        <input name="sch_tranx_<?php echo $indexCnt; ?>" id="sch_tranx_<?php echo $indexCnt; ?>" type="number" class="" 
                                        value="<?php echo $cSchool->sch_tranx; ?>" placeholder="enter the translate X" maxlength="20" />
                                    </td>  
                                    <td>                                    
                                        <input name="sch_trany_<?php echo $indexCnt; ?>" id="sch_trany_<?php echo $indexCnt; ?>" type="number" class="" 
                                        value="<?php echo $cSchool->sch_trany; ?>" placeholder="enter the translate Y" maxlength="20" />
                                    </td>    
                                    <td>                                    
                                        <input name="sch_linh_<?php echo $indexCnt; ?>" id="sch_linh_<?php echo $indexCnt; ?>" type="number" class="" 
                                        value="<?php echo $cSchool->sch_linh; ?>" placeholder="enter the Line Height" maxlength="20" />
                                    </td>                         
                                    <td>
                                        <input id="sch_logoimg_<?php echo $indexCnt; ?>" type="text" name="sch_logoimg_<?php echo $indexCnt; ?>"
                                            value="<?php echo $cSchool->sch_logoimg; ?>" readonly />
                                            
                                        <input id="upload_logoimg_button_<?php echo $indexCnt; ?>"
                                            data-target-textbox="sch_logoimg_<?php echo $indexCnt; ?>"
                                            type="button" class="button-primary media-picker-button" value="Select" />
                                    </td>
                                </tr> 
                            </tbody>
                        </table>

                        <table style="margin-bottom:35px">
                            <thead>
                                <tr>
                                    <th>School Logo Image Size</th>
                                    <th>School Big Image(1080*410)</th>
                                    <th>School Thumbnail One(1080*410)</th>
                                    <th>School Thumbnail Two(1080*410)</th>
                                    <th>School Description</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <tr> 
                                    <td> 
                                        <select name="sch_logoimg_size_<?php echo $indexCnt; ?>" id="sch_logoimg_size_<?php echo $indexCnt; ?>">
                                            <option value="200" <?php if ( $cSchool->sch_logoimg_size == 200 ) echo 'selected="selected"'; ?>>Large(200px)</option>
                                            <option value="150" <?php if ( $cSchool->sch_logoimg_size == 150 ) echo 'selected="selected"'; ?>>Medium(150px)</option>
                                            <option value="100" <?php if ( $cSchool->sch_logoimg_size == 100 ) echo 'selected="selected"'; ?>>Small(100px)</option>
                                            <option value="50" <?php if ( $cSchool->sch_logoimg_size == 50 ) echo 'selected="selected"'; ?>>Xsmall(50px)</option>
                                        </select>
                                    </td>
                                    <td>                                    
                                    <input id="sch_bimg_<?php echo $indexCnt; ?>" type="text" 
                                        name="sch_bimg_<?php echo $indexCnt; ?>"
                                            value="<?php echo $cSchool->sch_bimg; ?>" readonly />
                                            
                                        <input id="upload_bimg_button_<?php echo $indexCnt; ?>"
                                            data-target-textbox="sch_bimg_<?php echo $indexCnt; ?>"
                                            type="button" class="button-primary media-picker-button" value="Select" />
                                    </td>    
                                    <td>                                    
                                    <input id="sch_thimg1_<?php echo $indexCnt; ?>" type="text"
                                        name="sch_thimg1_<?php echo $indexCnt; ?>"
                                            value="<?php echo $cSchool->sch_thimg1; ?>" readonly />
                                            
                                        <input id="upload_thimg1_button_<?php echo $indexCnt; ?>"
                                            data-target-textbox="sch_thimg1_<?php echo $indexCnt; ?>"
                                            type="button" class="button-primary media-picker-button" value="Select" />
                                    </td>                         
                                    <td>
                                        <input id="sch_thimg2_<?php echo $indexCnt; ?>" type="text"
                                            name="sch_thimg2_<?php echo $indexCnt; ?>"
                                            value="<?php echo $cSchool->sch_thimg2; ?>" readonly />
                                            
                                        <input id="upload_thimg2_button_<?php echo $indexCnt; ?>"
                                            data-target-textbox="sch_thimg2_<?php echo $indexCnt; ?>"
                                            type="button" class="button-primary media-picker-button" value="Select" />
                                    </td>
                                    <td>                                    
                                        <textarea name="sch_des_<?php echo $indexCnt; ?>" id="new_sch_name" class="" rows="3" cols="30"><?php echo $cSchool->sch_des; ?></textarea>
                                    </td> 
                                </tr>
                            </tbody>
                        </table>

                        <hr>
                    </div>
                        <?php

                                // increment the index counter
                                $indexCnt++;
                            }
                        }
                        ?>
                    <table>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Update Schools">    
                                    <button type="button" class="button button-secondary btn-cancel-schools">Countries</button> 
                                </td>
                            <tr>
                        </tfoot>
                    </table> 

                </form>
                <script>
                    var coun_school_json = '<?php echo get_option('sm3_school_data_'.$curr_coun_code); ?>';  
                    // length of coun_code, coun_code array
                    var coun_code_json = '<?php echo get_option('sm3_coun_code'); ?>'; 


                    var can_school_json = '<?php echo get_option('sm3_school_data_can'); ?>';      
                    var usa_school_json = '<?php echo get_option('sm3_school_data_usa'); ?>';  
                    var eur_school_json = '<?php echo get_option('sm3_school_data_eur'); ?>';  
                    var mal_school_json = '<?php echo get_option('sm3_school_data_mal'); ?>';              
                    
                </script>

                <div>
                    <h2>Note the following:</h2>
                    <ul>
                        <li>only six schools will be added here</li>
                        <li>When you create <strong>Line Height</strong>, recommend 150 </li>
                        <li>When you create <strong>School logo image Size</strong>, please choose only one largest number (recommend 100px), choose smaller number (recommend 50px) for others  </li>
                        <li>When you create <strong>School Description</strong>, 20~30 characters recommended  </li>
                    </ul>
                </div>
                
            </div> 
            <?php
        }

        public function handleSchoolForm() {
            if(
                // get and check the nonce to prevent against CSF
                ! isset( $_POST['sm3_form'] ) ||
                ! wp_verify_nonce( $_POST['sm3_form'], 'sm3_update' )
            ){ ?>
                <div class="error">
                    <p>Sorry, your nonce was not correct. Please try again.</p>
                </div> <?php
                exit;
            } else {
                
                $valid_data = true;
                
                $curr_school_array = array();
                $curr_coun_code = $_POST['curr_coun_code'];
                // loop through the post fields and find the member fields, they begin 
                foreach($_POST as $key => $val) { 
                    // check if the current field is a position field
                    if(substr($key, 0, 7) === 'sch_num'){  

                        $row_index = substr($key, 8 - strlen($key));
                        // var_dump($row_index);
                        $sch_num = sanitize_text_field( $_POST[$key]);
                        $sch_name = sanitize_text_field( $_POST['sch_name_'.$row_index]);
                        $sch_tranx = sanitize_text_field( $_POST['sch_tranx_'.$row_index]);
                        $sch_trany = sanitize_text_field( $_POST['sch_trany_'.$row_index]); 
                        $sch_linh = sanitize_text_field( $_POST['sch_linh_'.$row_index]);
                        $sch_logoimg = sanitize_text_field( $_POST['sch_logoimg_'.$row_index]);
                        $sch_logoimg_size = sanitize_text_field( $_POST['sch_logoimg_size_'.$row_index]);   
                        $sch_bimg = sanitize_text_field( $_POST['sch_bimg_'.$row_index]);   
                        $sch_thimg1 = sanitize_text_field( $_POST['sch_thimg1_'.$row_index]);    
                        $sch_thimg2 = sanitize_text_field( $_POST['sch_thimg2_'.$row_index]); 
                        $sch_des = sanitize_text_field( $_POST['sch_des_'.$row_index]);

                        // form a team object
                        $curr_school = array(
                            "sch_num" => $sch_num,
                            "sch_name" => $sch_name,
                            "sch_tranx" => $sch_tranx,
                            "sch_trany" => $sch_trany,
                            "sch_linh" => $sch_linh,
                            "sch_logoimg" => $sch_logoimg,
                            "sch_logoimg_size" => $sch_logoimg_size,
                            "sch_bimg" => $sch_bimg,
                            "sch_thimg1" => $sch_thimg1,
                            "sch_thimg2" => $sch_thimg2,
                            "sch_des" => $sch_des,
                        );               
                        
                        array_push($curr_school_array, $curr_school);
                    }  
                }      
                
               // var_dump($curr_school_array);


                if($valid_data){
                    //update_option('sm3_school_data_'.$curr_coun_code, json_encode($curr_school_array));
                    update_option('sm3_school_data_'.$curr_coun_code, json_encode($curr_school_array));
                    update_option('sm3_coun_code', json_encode($curr_coun_code));
                    
                    ?><div class="updated">
                        <p>Schools updated. Click the <strong>Countries</strong> button to return to the Countries UI.</p>
                    </div><?php
                } else { 
                    ?><div class="error">
                        <p>There were one or more errors with your form data.</p>
                    </div><?php
                }
            }
        }
    
        
    }  

    // instantiates the plugin
    new Sm3_Schools();
}
?>