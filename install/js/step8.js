/**
 *    This file is part of "PCPIN Chat 6".
 *
 *    "PCPIN Chat 6" is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    "PCPIN Chat 6" is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var RepeatStep=0;

function initFinalCheckTables() {
  var importSettingNames=new Array();

  $('db_data_host').innerHTML=htmlspecialchars(window.parent.db_data['host']);
  $('db_data_user').innerHTML=htmlspecialchars(window.parent.db_data['user']);
  $('db_data_database').innerHTML=htmlspecialchars(window.parent.db_data['database']);
  $('db_data_prefix').innerHTML=htmlspecialchars(window.parent.db_data['prefix']);

  for (var i in window.parent.import_selection) {
    if (window.parent.import_selection[i]) {
      switch (i) {
        case 'users':
          importSettingNames.push('Users');
        break;
        case 'smilies':
          importSettingNames.push('Smilies');
        break;
        case 'rooms':
          importSettingNames.push('Rooms');
        break;
        case 'bad_words':
          importSettingNames.push('Bad words');
        break;
        case 'ip_filter':
          importSettingNames.push('IP Filter');
        break;
        case 'avatar_gallery':
          importSettingNames.push('Avatar Gallery');
        break;
        case 'banners':
          importSettingNames.push('Banners');
        break;
        case 'languages':
          importSettingNames.push('Languages');
        break;
      }
    }
  }
  if (importSettingNames.length==0) {
    $('import_settings_list').innerHTML=htmlspecialchars('None');
  } else {
    $('import_settings_list').innerHTML=htmlspecialchars(importSettingNames.join(', '));
  }
  var languages=new Array();
  for (var i=0; i<window.parent.languages.length; i++) {
    languages.push(window.parent.language_names[window.parent.languages[i]]);
  }
  $('install_languages_list').innerHTML=htmlspecialchars(languages.join(', '));

  if (window.parent.admin_account['create']) {
    $('administrator_account_no_new').style.display='none';
    $('administrator_account_username_row').style.display='';
    $('administrator_account_username').innerHTML=htmlspecialchars(window.parent.admin_account['username']);
    $('administrator_account_email_row').style.display='';
    $('administrator_account_email').innerHTML=htmlspecialchars(window.parent.admin_account['email']);
  } else {
    $('administrator_account_no_new').style.display='';
    $('administrator_account_username_row').style.display='none';
    $('administrator_account_email_row').style.display='none';
  }
}

function startInstallation() {
  if (confirm('Are you sure?')) {
    $('overview_tbl').style.display='none';
    $('installation_progress').style.display='';
    window.parent.lastStep=1;
    installStep(1);
  }
}
function installStep(step) {
  var import_fields=new Array();
  var progress_tbl=$('installation_progress');
  var tr=null;
  var td=null;

  if (typeof(step)=='number') {
    switch (step) {

      default:
        $('install_complete').style.display='';
      break;

      case 1: // Secure data
        RepeatStep=0;
        tr=progress_tbl.insertRow(progress_tbl.rows.length-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars('Storing data');
        setCssClass(td, 'tbl_row');
        td=tr.insertCell(-1);
        td.id='step_'+step+'_progress';
        td.innerHTML='<img src="./pic/progress_bar_267x14.gif" alt="'+htmlspecialchars('In progress...')+'" title="'+htmlspecialchars('In progress...')+'" />';
        setCssClass(td, 'tbl_row');
        td.style.fontWeight='bold';

        // Any data to import?
        for (var i in window.parent.import_selection) {
          if (window.parent.import_selection[i]) {
            import_fields.push(i);
          }
        }

        sendData('_CALLBACK_installStep('+step+')',
                 './install/ajax/secure_data.php',
                 'POST',
                 'host='+urlencode(window.parent.db_data['host'])
                +'&user='+urlencode(window.parent.db_data['user'])
                +'&password='+urlencode(window.parent.db_data['password'])
                +'&database='+urlencode(window.parent.db_data['database'])
                +'&prefix='+urlencode(window.parent.db_data['prefix'])
                +'&data_objects='+urlencode(import_fields.join(',')),
                true, false, 1000
                );
      break;

      case 2: // Create database structure
        RepeatStep=0;
        tr=progress_tbl.insertRow(progress_tbl.rows.length-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars('Creating database structure');
        setCssClass(td, 'tbl_row');
        td=tr.insertCell(-1);
        td.id='step_'+step+'_progress';
        td.innerHTML='<img src="./pic/progress_bar_267x14.gif" alt="'+htmlspecialchars('In progress...')+'" title="'+htmlspecialchars('In progress...')+'" />';
        setCssClass(td, 'tbl_row');
        td.style.fontWeight='bold';

        sendData('_CALLBACK_installStep('+step+')',
                 './install/ajax/create_db_structure.php',
                 'POST',
                 'host='+urlencode(window.parent.db_data['host'])
                +'&user='+urlencode(window.parent.db_data['user'])
                +'&password='+urlencode(window.parent.db_data['password'])
                +'&database='+urlencode(window.parent.db_data['database'])
                +'&prefix='+urlencode(window.parent.db_data['prefix']),
                true, false, 1000
                );
      break;

      case 3: // Fill tables with data
        RepeatStep=0;
        tr=progress_tbl.insertRow(progress_tbl.rows.length-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars('Installing data');
        setCssClass(td, 'tbl_row');
        td=tr.insertCell(-1);
        td.id='step_'+step+'_progress';
        td.innerHTML='<img src="./pic/progress_bar_267x14.gif" alt="'+htmlspecialchars('In progress...')+'" title="'+htmlspecialchars('In progress...')+'" />';
        setCssClass(td, 'tbl_row');
        td.style.fontWeight='bold';

        sendData('_CALLBACK_installStep('+step+')',
                 './install/ajax/fill_database.php',
                 'POST',
                 'host='+urlencode(window.parent.db_data['host'])
                +'&user='+urlencode(window.parent.db_data['user'])
                +'&password='+urlencode(window.parent.db_data['password'])
                +'&database='+urlencode(window.parent.db_data['database'])
                +'&prefix='+urlencode(window.parent.db_data['prefix']),
                true, false, 1000
                );
      break;

      case 4: // Importing stored data
        RepeatStep=0;
        tr=progress_tbl.insertRow(progress_tbl.rows.length-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars('Importing stored data');
        setCssClass(td, 'tbl_row');
        td=tr.insertCell(-1);
        td.id='step_'+step+'_progress';
        td.innerHTML='<img src="./pic/progress_bar_267x14.gif" alt="'+htmlspecialchars('In progress...')+'" title="'+htmlspecialchars('In progress...')+'" />';
        setCssClass(td, 'tbl_row');
        td.style.fontWeight='bold';

        sendData('_CALLBACK_installStep('+step+')',
                 './install/ajax/restore_data.php',
                 'POST',
                 'host='+urlencode(window.parent.db_data['host'])
                +'&user='+urlencode(window.parent.db_data['user'])
                +'&password='+urlencode(window.parent.db_data['password'])
                +'&database='+urlencode(window.parent.db_data['database'])
                +'&prefix='+urlencode(window.parent.db_data['prefix'])
                ,
                true, false, 1000
                );
      break;

      case 5: // Installing languages
        var lng_id=window.parent.languages[0];
        var tmp=new Array();
        for (var i=1; i<window.parent.languages.length; i++) {
          tmp.push(window.parent.languages[i]);
        }
        window.parent.languages=tmp;
        if (window.parent.languages.length>0) {
          RepeatStep=step;
        } else {
          RepeatStep=0;
        }
        var lng_name=window.parent.language_names[lng_id];
        var lng_filename=window.parent.language_files[lng_id];
        tr=progress_tbl.insertRow(progress_tbl.rows.length-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars('Installing language '+htmlspecialchars(lng_name));
        setCssClass(td, 'tbl_row');
        td=tr.insertCell(-1);
        td.id='step_'+step+'_progress';
        td.innerHTML='<img src="./pic/progress_bar_267x14.gif" alt="'+htmlspecialchars('In progress...')+'" title="'+htmlspecialchars('In progress...')+'" />';
        setCssClass(td, 'tbl_row');
        td.style.fontWeight='bold';

        sendData('_CALLBACK_installStep('+step+')',
                 './install/ajax/install_languages.php',
                 'POST',
                 'host='+urlencode(window.parent.db_data['host'])
                +'&user='+urlencode(window.parent.db_data['user'])
                +'&password='+urlencode(window.parent.db_data['password'])
                +'&database='+urlencode(window.parent.db_data['database'])
                +'&prefix='+urlencode(window.parent.db_data['prefix'])
                +'&iso_name='+urlencode(lng_id)
                +'&filename='+urlencode(lng_filename)
                +'&lng_name='+urlencode(lng_name)
                ,
                true, false, 1000
                );
      break;

      case 6: // Create admin account
        RepeatStep=0;
        tr=progress_tbl.insertRow(progress_tbl.rows.length-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars('Creating Administrator account');
        setCssClass(td, 'tbl_row');
        td=tr.insertCell(-1);
        td.id='step_'+step+'_progress';
        td.innerHTML='<img src="./pic/progress_bar_267x14.gif" alt="'+htmlspecialchars('In progress...')+'" title="'+htmlspecialchars('In progress...')+'" />';
        setCssClass(td, 'tbl_row');
        td.style.fontWeight='bold';

        sendData('_CALLBACK_installStep('+step+')',
                 './install/ajax/create_admin_account.php',
                 'POST',
                 'host='+urlencode(window.parent.db_data['host'])
                +'&user='+urlencode(window.parent.db_data['user'])
                +'&password='+urlencode(window.parent.db_data['password'])
                +'&database='+urlencode(window.parent.db_data['database'])
                +'&prefix='+urlencode(window.parent.db_data['prefix'])
                +'&do_create='+urlencode(window.parent.admin_account['create']? '1' : '0')
                +'&admin_username='+urlencode(window.parent.admin_account['username'])
                +'&admin_password='+urlencode(window.parent.admin_account['password'])
                +'&admin_email='+urlencode(window.parent.admin_account['email'])
                ,
                true, false, 1000
                );
      break;

      case 7: // Finalizing installation
        RepeatStep=0;
        tr=progress_tbl.insertRow(progress_tbl.rows.length-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars('Finalizing installation');
        setCssClass(td, 'tbl_row');
        td=tr.insertCell(-1);
        td.id='step_'+step+'_progress';
        td.innerHTML='<img src="./pic/progress_bar_267x14.gif" alt="'+htmlspecialchars('In progress...')+'" title="'+htmlspecialchars('In progress...')+'" />';
        setCssClass(td, 'tbl_row');
        td.style.fontWeight='bold';

        sendData('_CALLBACK_installStep('+step+')',
                 './install/ajax/finalize_installation.php',
                 'POST',
                 'host='+urlencode(window.parent.db_data['host'])
                +'&user='+urlencode(window.parent.db_data['user'])
                +'&password='+urlencode(window.parent.db_data['password'])
                +'&database='+urlencode(window.parent.db_data['database'])
                +'&prefix='+urlencode(window.parent.db_data['prefix'])
                +'&do_create='+urlencode(window.parent.admin_account['create']? '1' : '0')
                +'&admin_username='+urlencode(window.parent.admin_account['username'])
                +'&admin_password='+urlencode(window.parent.admin_account['password'])
                +'&admin_email='+urlencode(window.parent.admin_account['email'])
                ,
                true, false, 1000
                );
      break;

    }
  }
}
function _CALLBACK_installStep(step) {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var short_message=actionHandler.getCdata('short_message');

  $('step_'+step+'_progress').innerHTML=htmlspecialchars(short_message);

  if (status=='0') {
    // Success
    $('step_'+step+'_progress').style.color='#008800';
    installStep(RepeatStep>0? RepeatStep : step+1);
  } else {
    // An error
    $('step_'+step+'_progress').style.color='#ff0000';
    alert(message);
    if (status!='-1' && confirm('Continue installation?')) {
      installStep(RepeatStep>0? RepeatStep : step+1);
    }
  }
}

function openAdminPanel() {
  if (window.parent.admin_account['create']) {
    $('admin_panel_form').direct_login.value='1';
    $('admin_panel_form').login.value=window.parent.admin_account['username'];
    $('admin_panel_form').password.value=window.parent.admin_account['password'];
  } else {
    $('admin_panel_form').direct_login.value='';
  }
  $('admin_panel_form').submit();
}