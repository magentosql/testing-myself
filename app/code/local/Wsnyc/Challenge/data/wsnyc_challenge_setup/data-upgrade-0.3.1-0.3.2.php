<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$sampleContent = <<< EOT
<div class="main-banner">
    <img src="{{media url="wysiwyg/30daychallenge/request-banner.png"}}" alt="30 Day Clean Home Challenge" width="960" height="313"/>
</div>
<h1>Choose Your Free Sample of Laundry <br/> or Home Cleaning Products</h1>
<hr class="clean-challenge-separator"/>
<div class="main-description">
    <p>Not quite ready to commit to the 30-Day Challenge? That's okay.<br/> <strong>Sign up for our newsletter</strong> and we'll send you a Free Sample of a Laundry or Home Cleaning Product &ndash; your choice!</p>

    <p>Once you're ready to start spring cleaning, come back and enjoy 20% off any purchase<br/>
        of three or more products by using the code: <strong>30DAYCLEAN</strong> or pick up the <strong><a href="{{store url="spring-cleaning-bundle"}}">Spring Cleaning Bundle</a></strong><br/>
        - a $120 value for $80 - to get you started!</p>
</div>
<div class="request-form">
    <form action="{{store url="challenge/request"}}" method="post" id="request-form">
        <fieldset>
            <ul class="form-list">
                <li class="fields">
                    <div class="field">
                        <label class="required" for="firstname">First Name</label>

                        <div class="input-box">
                            <input id="firstname" class="input-text required-entry" title="First Name" type="text" name="firstname" value=""/>
                        </div>
                    </div>
                    <div class="field">
                        <label class="required" for="lastname">Last Name</label>

                        <div class="input-box">
                            <input id="lastname" class="input-text required-entry" title="Last Name" type="text" name="lastname" value=""/>
                        </div>
                    </div>
                    <div class="field">
                        <label class="required" for="email">Email Address</label>

                        <div class="input-box">
                            <input id="email" class="input-text required-entry" title="Email Address value=" type="text" name="email"/>
                        </div>
                    </div>
                </li>
                <li class="fields">
                    <div class="field">
                        <label class="required" for="street">Street Address</label>

                        <div class="input-box">
                            <input id="street" class="input-text required-entry" title="Street Address" type="text" name="street[]" value=""/>
                        </div>
                    </div>
                    <div class="field">
                        <label class="required" for="city">City</label>

                        <div class="input-box">
                            <input id="city" class="input-text required-entry" title="City" type="text" name="city" value=""/>
                        </div>
                    </div>
                    <div class="field">
                        <div class="row-field width-70">
                            <label class="required" for="region_id">State</label>

                            <div class="input-box">
                                <input id="country" type="hidden" name="country" value="US"/>
                                <select id="region_id" class="validate-select" title="State" name="region_id">
                                    <option value="">Please select region, state or province</option>
                                    <option title="Alabama" value="1">Alabama</option>
                                    <option title="Alaska" value="2">Alaska</option>
                                    <option title="American Samoa" value="3">American Samoa</option>
                                    <option title="Arizona" value="4">Arizona</option>
                                    <option title="Arkansas" value="5">Arkansas</option>
                                    <option title="Armed Forces Africa" value="6">Armed Forces Africa</option>
                                    <option title="Armed Forces Americas" value="7">Armed Forces Americas</option>
                                    <option title="Armed Forces Canada" value="8">Armed Forces Canada</option>
                                    <option title="Armed Forces Europe" value="9">Armed Forces Europe</option>
                                    <option title="Armed Forces Middle East" value="10">Armed Forces Middle East</option>
                                    <option title="Armed Forces Pacific" value="11">Armed Forces Pacific</option>
                                    <option title="California" value="12">California</option>
                                    <option title="Colorado" value="13">Colorado</option>
                                    <option title="Connecticut" value="14">Connecticut</option>
                                    <option title="Delaware" value="15">Delaware</option>
                                    <option title="District of Columbia" value="16">District of Columbia</option>
                                    <option title="Federated States Of Micronesia" value="17">Federated States Of Micronesia</option>
                                    <option title="Florida" value="18">Florida</option>
                                    <option title="Georgia" value="19">Georgia</option>
                                    <option title="Guam" value="20">Guam</option>
                                    <option title="Hawaii" value="21">Hawaii</option>
                                    <option title="Idaho" value="22">Idaho</option>
                                    <option title="Illinois" value="23">Illinois</option>
                                    <option title="Indiana" value="24">Indiana</option>
                                    <option title="Iowa" value="25">Iowa</option>
                                    <option title="Kansas" value="26">Kansas</option>
                                    <option title="Kentucky" value="27">Kentucky</option>
                                    <option title="Louisiana" value="28">Louisiana</option>
                                    <option title="Maine" value="29">Maine</option>
                                    <option title="Marshall Islands" value="30">Marshall Islands</option>
                                    <option title="Maryland" value="31">Maryland</option>
                                    <option title="Massachusetts" value="32">Massachusetts</option>
                                    <option title="Michigan" value="33">Michigan</option>
                                    <option title="Minnesota" value="34">Minnesota</option>
                                    <option title="Mississippi" value="35">Mississippi</option>
                                    <option title="Missouri" value="36">Missouri</option>
                                    <option title="Montana" value="37">Montana</option>
                                    <option title="Nebraska" value="38">Nebraska</option>
                                    <option title="Nevada" value="39">Nevada</option>
                                    <option title="New Hampshire" value="40">New Hampshire</option>
                                    <option title="New Jersey" value="41">New Jersey</option>
                                    <option title="New Mexico" value="42">New Mexico</option>
                                    <option title="New York" value="43">New York</option>
                                    <option title="North Carolina" value="44">North Carolina</option>
                                    <option title="North Dakota" value="45">North Dakota</option>
                                    <option title="Northern Mariana Islands" value="46">Northern Mariana Islands</option>
                                    <option title="Ohio" value="47">Ohio</option>
                                    <option title="Oklahoma" value="48">Oklahoma</option>
                                    <option title="Oregon" value="49">Oregon</option>
                                    <option title="Palau" value="50">Palau</option>
                                    <option title="Pennsylvania" value="51">Pennsylvania</option>
                                    <option title="Puerto Rico" value="52">Puerto Rico</option>
                                    <option title="Rhode Island" value="53">Rhode Island</option>
                                    <option title="South Carolina" value="54">South Carolina</option>
                                    <option title="South Dakota" value="55">South Dakota</option>
                                    <option title="Tennessee" value="56">Tennessee</option>
                                    <option title="Texas" value="57">Texas</option>
                                    <option title="Utah" value="58">Utah</option>
                                    <option title="Vermont" value="59">Vermont</option>
                                    <option title="Virgin Islands" value="60">Virgin Islands</option>
                                    <option title="Virginia" value="61">Virginia</option>
                                    <option title="Washington" value="62">Washington</option>
                                    <option title="West Virginia" value="63">West Virginia</option>
                                    <option title="Wisconsin" value="64">Wisconsin</option>
                                    <option title="Wyoming" value="65">Wyoming</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-field width-30">
                            <label class="required" for="postcode">ZIP</label>

                            <div class="input-box">
                                <input id="postcode" class="input-text required-entry" title="ZIP" type="text" name="postcode" value=""/>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="control">
                    <input type="hidden" name="newsletter" value="0"/>
                    <input type="checkbox" name="newsletter" value="1" title="Newsletter" id="newsletter" checked="checked" class="checkbox"/>
                    <label for="newsletter">Subscribe me to the newsletter</label>
                </li>
                <li class="wide">
                    <div class="input-box">
                        <select name="sample" id="sample" class="validate-select" title="Choose Your Sample" placeholder="Choose Your Sample">
                            <option class="label" disabled selected value="" style="display:none;">Choose Your Free Sample of Laundry or Home Cleaning Product</option>
                            <option title="All-Purpose Bleach Alternative" value="Bleach-PQ">All-Purpose Bleach Alternative</option>
                            <option title="Dish Detergent" value="Dish-PQ">Dish Detergent</option>
                            <option title="Sport Detergent" value="Sport-PQ">Sport Detergent</option>
                            <option title="Denim Wash" value="Denim-PQ">Denim Wash</option>
                            <option title="Fabric Conditioner Classic" value="ClassicConditioner-PQ">Fabric Conditioner Classic</option>
                        </select>
                    </div>
                </li>
            </ul>
            <div class="buttons-set" id="billing-buttons-container">
                <button class="button btn-cart" title="Add to Bag" type="submit"><span><span>Get My Free Sample</span></span></button>
            </div>
        </fieldset>
    </form>
</div>
<div class="details"><span class="label">Promotion Details</span>
    <ul>
        <li>Promotional offer period begins April 20, 2015 at 12:01am EST and ends June 10, 2015 at 11:59pm EST</li>
        <li>One sample per user</li>
        <li>Valid while supplies last</li>
        <li>The Spring Cleaning Bundle is available while supplies last</li>
    </ul>
    <span class="call">Please call us at (212) 209-0074 with any questions</span>
</div>
<script type="text/javascript">
//<![CDATA[
    var form = new VarienForm('request-form');
//]]>
</script>
EOT;

$cmsPageData = array(
    'title' => '30 Day Clean Home Challenge Sample Request',
    'root_template' => 'one_column',
    'identifier' => '30-day-clean-home-challenge-sample-request',
    'stores' => array(0),//available for all store views
    'content' => $sampleContent,
    'custom_layout_update_xml' => '<remove name="breadcrumbs" /><reference name="head"><action method="addItem"><type>skin_css</type><file>css/clean-challenge.css</file></action></reference>'
);

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-sample-request', 'identifier');
$page->addData($cmsPageData)->save();


$installer->endSetup();