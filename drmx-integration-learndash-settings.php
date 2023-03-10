<div class="wrap">
    <h1>Website Integration Preferences -- LearnDash</h1>
    <hr style="margin: 10px 0px 15px; ">
    <?php
        if($_POST){
            foreach($_POST as $key=>$value){
                if($key != 'submit'){
                    update_option($key, $value);
                }
            }
        }
    ?>
    <form method="post" action="" novalidate="novalidate">
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="drmx_account">DRM-X 4.0 Account (Email) <span style="color: red">*</span></label>
                </th>
                <td>
                    <input name="drmx_account" type="text" id="drmx_account" value="<?php echo get_option('drmx_account'); ?>" style="width: 360px">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="drmx_authentication">DRM-X 4.0 Web Service Authentication String <span style="color: red">*</span></label>
                </th>
                <td>
                    <input name="drmx_authentication" type="text" id="drmx_authentication" value="<?php echo get_option('drmx_authentication'); ?>" style="width: 360px">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="drmx_wsdl">DRM-X 4.0 XML Web Service URL <span style="color: red">*</span></label>
                </th>
                <td>
                    <input name="drmx_wsdl" type="text" id="drmx_rightsid" value="<?php if(empty(get_option('drmx_wsdl'))){ echo 'http://4.drm-x.com/haihaisoftlicenseservice.asmx?wsdl'; }else{ echo get_option('drmx_wsdl'); } ?>" class="regular-text" style="width: 360px">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="drmx_groupid">DRM-X 4.0 GroupID <span style="color: red">*</span></label>
                </th>
                <td>
                    <input name="drmx_groupid" type="number" id="drmx_groupid" value="<?php echo get_option('drmx_groupid'); ?>" style="width: 360px">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="drmx_rightsid">DRM-X 4.0 RightsID <span style="color: red">*</span></label>
                </th>
                <td>
                    <input name="drmx_rightsid" type="number" id="drmx_rightsid" value="<?php echo get_option('drmx_rightsid'); ?>" style="width: 360px">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="drmx_binding">User Bind Count <span style="color: red">*</span></label>
                </th>
                <td>
                    <input name="drmx_binding" type="number" id="drmx_binding" value="<?php if(empty(get_option('drmx_binding'))){ echo '2'; }else{ echo get_option('drmx_binding'); }?>" style="width: 360px">
                </td>
            </tr>

        </table>
        <p>If you are a DRM-X 4.0 China account, please set the XML Web Service URL to: http://4.drm-x.cn/haihaisoftlicenseservice.asmx?wsdl</p>
        <p>The user binding count is only valid for users who have not yet acquired a license. <br>If you want to modify the user who has already acquired the license, please login your DRM-X 4.0 account, edit the user, reset the user binding count.</p>
        <p>Finally, Please login your DRM-X 4.0 account -- Account Settings -- 	Website Integration Preferences -- Custom login page integration, <br> Set the corresponding Web Service Authentication, and set the License URL to: <span style="color: blue"> <?php echo site_url();?>/wp-content/plugins/drmx-integration-learndash/drmx_index.php</span></p>
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Settings">
    </form>

    <div style="margin-top: 20px">
        <hr>
        <p>The plugin contains online video embedding shortcode, For example: [xvast-player]https://www.yourwebsite.com/upload/video/sample_video_P.mp4[/xvast-player]</p>
    </div>

</div>