



<!-- Main content ---------------------------------------------------------- -->
<section class="content">
    <div class="plugin-filters">
        <div class="input-group col-sm-4">
            <label class="control-label col-sm-3"><?= lang('Plugins_Filters_Mac');?></label>
            <input class="form-control col-sm-3" id="txtMacFilter" type="text" value="--" readonly>
        </div>
    </div>
    <div class="nav-tabs-custom plugin-content" style="margin-bottom: 0px;">
        
        <ul id="tabs-location" class="nav nav-tabs col-sm-2 ">
            <!-- PLACEHOLDER -->
        </ul>  
        <div id="tabs-content-location" class="tab-content col-sm-10"> 
            <!-- PLACEHOLDER -->
        </div>   
    
</section>


<script defer>

// -----------------------------------------------------------------------------
// Initializes fields based on current MAC
function initFields() {   

    var urlParams = new URLSearchParams(window.location.search);
    mac = urlParams.get ('mac');    

    // if the current mac has changed, reinitialize the data
    if(mac != undefined && $("#txtMacFilter").val() != mac)
    {
        $("#txtMacFilter").val(mac);
        console.log("UPDATE");

        getData();
    }

}

// -----------------------------------------------------------------------------
// Checking if current MAC has changed and triggering an updated if needed
function updater() {   

    initFields()

    // loop
    setTimeout(function() {
        updater();
    }, 500);   
}

// -----------------------------------------------------------------------------
// Get form control according to the column definition from config.json > database_column_definitions
function getFormControl(dbColumnDef, value, index) {    

    result = ''

    switch(dbColumnDef.type)
    {
        case 'label':            
            result = `<span>${value}<span>`;
            break;
        case 'textboxsave':

            value = value == 'null' ? '' : value; // hide 'null' values

            id = `${dbColumnDef.column}_${index}`

            result =    `<span class="form-group">
                            <div class="input-group">
                                <input class="form-control" type="text" value="${value}" id="${id}" data-my-column="${dbColumnDef.column}"  data-my-index="${index}" name="${dbColumnDef.column}">
                                <span class="input-group-addon"><i class="fa fa-save pointer" onclick="saveData('${id}');"></i></span>
                            </div>
                        <span>`;
            break;
        case 'url':
            result = `<span><a href="${value}" target="_blank">${value}</a><span>`;
            break;
        case 'devicemac':
            result = `<span class="anonymizeMac"><a href="/deviceDetails.php?mac=${value}" target="_blank">${value}</a><span>`;
            break;
        case 'deviceip':
            result = `<span class="anonymizeIp"><a href="#" onclick="navigateToDeviceWithIp('${value}')" >${value}</a><span>`;
            break;
        case 'threshold': 
            $.each(dbColumnDef.options, function(index, obj) {
                if(Number(value) < obj.maximum && result == '')
                {
                    result = `<div style="background-color:${obj.hexColor}">${value}</div>`
                    // return;
                }
            });
            break;
        case 'replace': 
            $.each(dbColumnDef.options, function(index, obj) {
                if(value == obj.equals)
                {
                    result = `<span title="${value}">${obj.replacement}</span>`
                }
            });
            break;
        default:
            result = value;           
    }

    return result;
}

// -----------------------------------------------------------------------------
// Update the corresponding DB column and entry
function saveData (id) {
    columnName  = $(`#${id}`).attr('data-my-column')
    index  = $(`#${id}`).attr('data-my-index')
    columnValue = $(`#${id}`).val()

    $.get(`php/server/dbHelper.php?action=update&dbtable=Plugins_Objects&columnName=Index&id=${index}&columns=UserData&values=${columnValue}`, function(data) {
    
        // var result = JSON.parse(data);
        console.log(data) 

        if(sanitize(data) == 'OK')
        {          
          showMessage('<?= lang('Gen_DataUpdatedUITakesTime');?>')          
          // Remove navigation prompt "Are you sure you want to leave..."
          window.onbeforeunload = null;
        } else
        {
          showMessage('<?= lang('Gen_LockedDB');?>')           
        }        

    });    
}

// -----------------------------------------------------------------------------
// Get translated string
function localize (obj, key) {

    currLangCode = getCookie("language")

    result = ""
    en_us = ""

    if(obj.localized && obj.localized.includes(key))
    {
        for(i=0;i<obj[key].length;i++)
        {
            code = obj[key][i]["language_code"]            

            if( code == 'en_us')
            {
                en_us = obj[key][i]["string"]
            }

            if(code == currLangCode)
            {
                result = obj[key][i]["string"]
            }

        }
    }

    result == "" ? result = en_us : result ;

    return result;
}

// -----------------------------------------------------------------------------
pluginDefinitions = []
pluginUnprocessedEvents = []
pluginObjects = []
pluginHistory = []

function getData(){

    $.get('api/plugins.json', function(res) {    
        
        pluginDefinitions = res["data"];

        $.get('api/table_plugins_events.json', function(res) {

            pluginUnprocessedEvents = res["data"];

            $.get('api/table_plugins_objects.json', function(res) {

                pluginObjects = res["data"];
                
                $.get('api/table_plugins_history.json', function(res) {                

                    pluginHistory = res["data"];

                    generateTabs()

                });
            });
        });

    });
}

// -----------------------------------------------------------------------------
function generateTabs()
{
    activetab = 'active'

    //  clear previous headers data
    $('#tabs-location').html("");
    //  clear previous content data
    $('#tabs-content-location').html("");

    $.each(pluginDefinitions, function(index, pluginObj) {

        // console.log(pluginObj)

        if(pluginObj.show_ui) // hiding plugins where specified
        {
            $('#tabs-location').append(
                `<li class=" left-nav ${activetab}">
                    <a class=" col-sm-12  " href="#${pluginObj.unique_prefix}" data-plugin-prefix="${pluginObj.unique_prefix}" id="${pluginObj.unique_prefix}_id" data-toggle="tab" >
                    ${localize(pluginObj, 'icon')} ${localize(pluginObj, 'display_name')}
                    </a>
                </li>`
            );
            activetab = '' // only first tab is active
        }
    });

    activetab = 'active'
    
    $.each(pluginDefinitions, function(index, pluginObj) {

        headersHtml = ""        
        colDefinitions = []
        evRows = ""
        obRows = ""
        hiRows = ""

        // Generate the header
        $.each(pluginObj["database_column_definitions"], function(index, colDef){
            if(colDef.show == true) // select only the ones to show
            {
                colDefinitions.push(colDef)            
                headersHtml += `<th class="${colDef.css_classes}" >${localize(colDef, "name" )}</th>`
            }
        });

        // Generate the event rows
        var eveCount = 0;
        for(i=0;i<pluginUnprocessedEvents.length;i++)
        {
            if(pluginUnprocessedEvents[i].Plugin == pluginObj.unique_prefix)
            {
                clm = ""

                for(j=0;j<colDefinitions.length;j++) 
                {   
                    clm += '<td>'+ pluginUnprocessedEvents[i][colDefinitions[j].column] +'</td>'
                }                   
                evRows += `<tr data-my-index="${pluginUnprocessedEvents[i]["Index"]}" >${clm}</tr>`
                eveCount++;
            }            
        }      

        // Generate the history rows        
        var histCount = 0
        var histCountDisplayed = 0
        
        for(i=pluginHistory.length-1;i >= 0;i--) // from latest to the oldest
        {
            if(pluginHistory[i].Plugin == pluginObj.unique_prefix)
            {
                if(histCount < 50) // only display 50 entries to optimize performance
                {       
                    clm = ""

                    for(j=0;j<colDefinitions.length;j++) 
                    {   
                        clm += '<td>'+ pluginHistory[i][colDefinitions[j].column] +'</td>'
                    }            
                    hiRows += `<tr data-my-index="${pluginHistory[i]["Index"]}" >${clm}</tr>`

                    histCountDisplayed++;
                }
                histCount++; // count and display the total
            }            
        }        

        // Generate the object rows
        var obCount = 0;
        for(var i=0;i<pluginObjects.length;i++)
        {
            if(pluginObjects[i].Plugin == pluginObj.unique_prefix)
            {
                if(shouldBeShown(pluginObjects[i], pluginObj)) // filter TODO
                {
                    clm = ""

                    for(var j=0;j<colDefinitions.length;j++) 
                    {   
                        clm += '<td>'+ getFormControl(colDefinitions[j], pluginObjects[i][colDefinitions[j].column], pluginObjects[i]["Index"]) +'</td>'
                    }                                   
                    obRows += `<tr data-my-index="${pluginObjects[i]["Index"]}" >${clm}</tr>`
                    obCount++;
                }
            }            
        }

        // Generate the HTML

        $('#tabs-content-location').append(
            `    
            <div id="${pluginObj.unique_prefix}" class="tab-pane ${activetab}">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <ul class="nav nav-tabs">
                        <li class="active" >
                            <a href="#objectsTarget_${pluginObj.unique_prefix}" data-toggle="tab" >
                                
                                <i class="fa fa-cube"></i> <?= lang('Plugins_Objects');?> (${obCount})
                                
                            </a>
                        </li>

                        <li >
                            <a href="#eventsTarget_${pluginObj.unique_prefix}" data-toggle="tab" >
                                
                                <i class="fa fa-bolt"></i> <?= lang('Plugins_Unprocessed_Events');?> (${eveCount})
                                
                            </a>
                        </li>

                        <li >
                            <a href="#historyTarget_${pluginObj.unique_prefix}" data-toggle="tab" >
                                
                                <i class="fa fa-clock"></i> <?= lang('Plugins_History');?> (${histCountDisplayed} <?= lang('Plugins_Out_of');?> ${histCount})
                                
                            </a>
                        </li>

                    <ul>
                </div>
                

                <div class="tab-content">

                    <div id="objectsTarget_${pluginObj.unique_prefix}" class="tab-pane ${activetab}">
                        <table class="table table-striped" data-my-dbtable="Plugins_Objects"> 
                            <tbody>
                                <tr>
                                    ${headersHtml}                            
                                </tr>  
                                ${obRows}
                            </tbody>
                        </table>
                        <div class="plugin-obj-purge">                                 
                            <button class="btn btn-primary" onclick="purgeAll('${pluginObj.unique_prefix}', 'Plugins_Objects' )"><?= lang('Plugins_DeleteAll');?></button> 
                        </div> 
                    </div>
                    <div id="eventsTarget_${pluginObj.unique_prefix}" class="tab-pane">
                        <table class="table table-striped" data-my-dbtable="Plugins_Events">
                        
                            <tbody>
                                <tr>
                                    ${headersHtml}                            
                                </tr>  
                                ${evRows}
                            </tbody>
                        </table>
                        <div class="plugin-obj-purge">                                 
                            <button class="btn btn-primary" onclick="purgeAll('${pluginObj.unique_prefix}', 'Plugins_Events' )"><?= lang('Plugins_DeleteAll');?></button> 
                        </div> 
                    </div>    
                    <div id="historyTarget_${pluginObj.unique_prefix}" class="tab-pane">
                        <table class="table table-striped" data-my-dbtable="Plugins_History">
                        
                            <tbody>
                                <tr>
                                    ${headersHtml}                            
                                </tr>  
                                ${hiRows}
                            </tbody>
                        </table>
                        <div class="plugin-obj-purge">                                 
                            <button class="btn btn-primary" onclick="purgeAll('${pluginObj.unique_prefix}', 'Plugins_History' )"><?= lang('Plugins_DeleteAll');?></button> 
                        </div> 
                    </div>                   


                </div>

                <div class='plugins-description'>

                    ${localize(pluginObj, 'description')} 
                
                    <span>
                        <a href="https://github.com/jokob-sk/Pi.Alert/tree/main/front/plugins/${pluginObj.code_name}" target="_blank"><?= lang('Gen_ReadDocs');?></a>
                    </span>
                
                </div>
            </div>
        `);

        activetab = '' // only first tab is active
    });

    initTabs()    
}

// --------------------------------------------------------
// Handle active / selected tabs
// handle first tab (objectsTarget_) display 
function initTabs()
{
    // events on tab change
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href") // activated tab
       
        // save the last prefix
        if(target.includes('_') == false )
        {
            pref = target.split('#')[1]
        } else
        {
            pref = target.split('_')[1]
        } 

        everythingHidden = false;

        if($('#objectsTarget_'+ pref) != undefined && $('#historyTarget_'+ pref) != undefined && $('#eventsTarget_'+ pref) != undefined)
        {
            everythingHidden = $('#objectsTarget_'+ pref).attr('class').includes('active') == false && $('#historyTarget_'+ pref).attr('class').includes('active') == false && $('#eventsTarget_'+ pref).attr('class').includes('active') == false;
        }

        // show the objectsTarget if no specific pane selected or if selected is hidden        
        if((target == '#'+pref ) && everythingHidden) 
        {
            var classTmp = $('#objectsTarget_'+ pref).attr('class');

            if($('#objectsTarget_'+ pref).attr('class').includes('active') == false)
            {   
                classTmp += ' active';            
                $('#objectsTarget_'+ pref).attr('class', classTmp)
            }
        } 
    });
}

// --------------------------------------------------------
// Filter method that determines if an entry should be shown
function shouldBeShown(entry, pluginObj)
{    
    if (pluginObj.hasOwnProperty('data_filters')) {
        
        let dataFilters = pluginObj.data_filters;

        // Loop through 'data_filters' array and appply filters on individual plugin entries
        for (let i = 0; i < dataFilters.length; i++) {
            
            compare_field_id = dataFilters[i].compare_field_id;
            compare_column = dataFilters[i].compare_column;
            compare_operator = dataFilters[i].compare_operator;
            compare_js_template = dataFilters[i].compare_js_template;
            compare_use_quotes = dataFilters[i].compare_use_quotes;
            compare_field_id_value = $(`#${compare_field_id}`).val();            

            if(compare_field_id_value != undefined && compare_field_id_value != '--') 
            {
                // valid value                
                // resolve the left and right part of the comparison 
                let left = compare_js_template.replace('{value}', `${compare_field_id_value}`)
                let right = compare_js_template.replace('{value}', `${entry[compare_column]}`)

                // include wrapper quotes if specified
                compare_use_quotes ? quotes = '"' : quotes = ''                 

                result =  eval(
                                quotes +  `${eval(left)}` + quotes + 
                                                ` ${compare_operator} ` + 
                                quotes +  `${eval(right)}` + quotes 
                            ); 

                return result;                              
            }
        }
    }
    return true;
}

// --------------------------------------------------------
// Data cleanup/purge functionality
plugPrefix = ''
dbTable    = ''

function purgeAll(callback) {
  plugPrefix = arguments[0];  // plugin prefix
  dbTable    = arguments[1];  // DB table
  // Ask 
  showModalWarning('<?= lang('Gen_Purge');?>' + ' ' + plugPrefix + ' ' + dbTable , '<?= lang('Gen_AreYouSure');?>',
    '<?= lang('Gen_Cancel');?>', '<?= lang('Gen_Okay');?>', "purgeAllExecute");
}

// --------------------------------------------------------
function purgeAllExecute() {
    $.ajax({
        method: "POST",
        url: "php/server/dbHelper.php",
        data: { action: "delete", dbtable: dbTable, columnName: 'Plugin', id:plugPrefix },
        success: function(data, textStatus) {
            showModalOk ('Result', data );
        }
    })

}

// --------------------------------------------------------
function purgeVisible() {

    idArr = $(`#${plugPrefix} table[data-my-dbtable="${dbTable}"] tr[data-my-index]`).map(function(){return $(this).attr("data-my-index");}).get();

    $.ajax({
        method: "POST",
        url: "php/server/dbHelper.php",
        data: { action: "delete", dbtable: dbTable, columnName: 'Index', id:idArr.toString() },
        success: function(data, textStatus) {
            showModalOk ('Result', data );
        }
    })

}


// -----------------------------------------------------------------------------
// Main sequence

getData()
updater()

</script>
