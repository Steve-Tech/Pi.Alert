<!-- ---------------------------------------------------------------------------
#  Pi.Alert
#  Open Source Network Guard / WIFI & LAN intrusion detector 
#
#  devices.php - Front module. Devices list page
#-------------------------------------------------------------------------------
#  Puche 2021 / 2022+ jokob             jokob@duck.com                GNU GPLv3
#--------------------------------------------------------------------------- -->

<?php

  require 'php/templates/header.php';
  require 'php/templates/graph.php';
  require 'php/server/util.php';
  

  // check permissions
  $dbPath = "../db/pialert.db";
  $confPath = "../config/pialert.conf";

  checkPermissions([$dbPath, $confPath]);
?>

<!-- Page ------------------------------------------------------------------ -->
  <div class="content-wrapper">

<!-- Content header--------------------------------------------------------- -->
    <section class="content-header">
      <h1 id="pageTitle">
           <?php echo lang('Device_Title');?>
      </h1>
    </section>

<!-- Main content ---------------------------------------------------------- -->
    <section class="content">

<!-- top small box 1 ------------------------------------------------------- -->
      <div class="row">
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getDevicesList('all');">
          <div class="small-box bg-aqua">
            <div class="inner"><h3 id="devicesAll"> -- </h3>
                <p class="infobox_label"><?php echo lang('Device_Shortcut_AllDevices');?></p>
            </div>
            <div class="icon"><i class="fa fa-laptop text-aqua-40"></i></div>
          </div>
          </a>
        </div>
        
<!-- top small box 2 ------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getDevicesList('connected');">
          <div class="small-box bg-green">
            <div class="inner"><h3 id="devicesConnected"> -- </h3>
                <p class="infobox_label"><?php echo lang('Device_Shortcut_Connected');?></p>
            </div>
            <div class="icon"><i class="fa fa-plug text-green-40"></i></div>
          </div>
          </a>
        </div>

<!-- top small box 3 ------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getDevicesList('favorites');">
          <div class="small-box bg-yellow">
            <div class="inner"><h3 id="devicesFavorites"> -- </h3>
                <p class="infobox_label"><?php echo lang('Device_Shortcut_Favorites');?></p>
            </div>
            <div class="icon"><i class="fa fa-star text-yellow-40"></i></div>
          </div>
          </a>
        </div>

<!-- top small box 4 ------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getDevicesList('new');">
          <div class="small-box bg-yellow">
            <div class="inner"><h3 id="devicesNew"> -- </h3>
                <p class="infobox_label"><?php echo lang('Device_Shortcut_NewDevices');?></p>
            </div>
            <div class="icon"><i class="ion ion-plus-round text-yellow-40"></i></div>
          </div>
          </a>
        </div>

<!-- top small box 5 ------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getDevicesList('down');">
          <div class="small-box bg-red">
            <div class="inner"><h3 id="devicesDown"> -- </h3>
                <p class="infobox_label"><?php echo lang('Device_Shortcut_DownAlerts');?></p>
            </div>
            <div class="icon"><i class="fa fa-warning text-red-40"></i></div>
          </div>
          </a>
        </div>

<!-- top small box 6 ------------------------------------------------------- -->
        <div class="col-lg-2 col-sm-4 col-xs-6">
          <a href="#" onclick="javascript: getDevicesList('archived');">
          <div class="small-box bg-gray top_small_box_gray_text">
            <div class="inner"><h3 id="devicesArchived"> -- </h3>
                <p class="infobox_label"><?php echo lang('Device_Shortcut_Archived');?></p>
            </div>
            <div class="icon"><i class="fa fa-eye-slash text-gray-40"></i></div>
          </div>
          </a>
        </div>

      </div>

<!-- Activity Chart ------------------------------------------------------- -->

      <div class="row">
          <div class="col-md-12">
          <div class="box" id="clients">
              <div class="box-header with-border">
                <h3 class="box-title"><?php echo lang('Device_Shortcut_OnlineChart');?> </h3>
              </div>
              <div class="box-body">
                <div class="chart">
                  <script src="lib/AdminLTE/bower_components/chart.js/Chart.js"></script>
                  <canvas id="OnlineChart" style="width:100%; height: 150px;  margin-bottom: 15px;"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
          </div>
      </div>
      <script src="js/graph_online_history.js"></script>
      <script>
        var pia_js_online_history_time = [<?php pia_graph_devices_data($Pia_Graph_Device_Time); ?>];
        var pia_js_online_history_ondev = [<?php pia_graph_devices_data($Pia_Graph_Device_Online); ?>];
        var pia_js_online_history_dodev = [<?php pia_graph_devices_data($Pia_Graph_Device_Down); ?>];
        var pia_js_online_history_ardev = [<?php pia_graph_devices_data($Pia_Graph_Device_Arch); ?>];
        pia_draw_graph_online_history(pia_js_online_history_time, pia_js_online_history_ondev, pia_js_online_history_dodev, pia_js_online_history_ardev);
      </script>

<!-- datatable ------------------------------------------------------------- -->
      <div class="row">
        <div class="col-xs-12">
          <div id="tableDevicesBox" class="box">

            <!-- box-header -->
            <div class="box-header">
              <h3 id="tableDevicesTitle" class="box-title text-gray">Devices</h3>
            </div>

            <!-- table -->
            <div class="box-body table-responsive">
              <table id="tableDevices" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>                
                  
                </tr>
                </thead>
              </table>
            </div>
            <!-- /.box-body -->

          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

<!-- ----------------------------------------------------------------------- -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


<!-- ----------------------------------------------------------------------- -->
<?php
  require 'php/templates/footer.php';
?>


<!-- ----------------------------------------------------------------------- -->
<!-- Datatable -->
  <link rel="stylesheet" href="lib/AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <script src="lib/AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="lib/AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


<!-- page script ----------------------------------------------------------- -->
<script>
  var deviceStatus    = 'all';
  var parTableRows    = 'Front_Devices_Rows';
  var parTableOrder   = 'Front_Devices_Order';
  var tableRows       = 10;
  var tableOrder      = [[3,'desc'], [0,'asc']];
  var tableColumnVisible = [0,1,2,3,4,5,6,7,8,9,10,12,13]
  var tableColumnOrder = [0,1,2,3,4,5,6,7,8,9,10,12,13]
  var tableColumnAll  = [0,1,2,3,4,5,6,7,8,9,10,12,13]

  // Read parameters & Initialize components
  main();


// -----------------------------------------------------------------------------
function main () {

  // get visible columns
  $.get('php/server/parameters.php?action=get&expireMinutes=525600&defaultValue=[0,1,2,3,4,5,6,7,8,9,10,11,12,13]&parameter=Front_Devices_Columns_Visible', function(data) {
    
    tableColumnVisible = numberArrayFromString(data);

    // get the custom order specified by the user
    $.get('php/server/parameters.php?action=get&expireMinutes=525600&defaultValue=[0,1,2,3,4,5,6,7,8,9,10,11,12,13]&parameter=Front_Devices_Columns_Order', function(data) {
    
      tableColumnOrder = numberArrayFromString(data);

      //initialize the table headers in the correct order
      var headersDefaultOrder = [ '<?php echo lang('Device_TableHead_Name');?>',
                                  '<?php echo lang('Device_TableHead_Owner');?>',
                                  '<?php echo lang('Device_TableHead_Type');?>',       
                                  '<?php echo lang('Device_TableHead_Icon');?>',
                                  '<?php echo lang('Device_TableHead_Favorite');?>',
                                  '<?php echo lang('Device_TableHead_Group');?>',
                                  '<?php echo lang('Device_TableHead_FirstSession');?>',
                                  '<?php echo lang('Device_TableHead_LastSession');?>',
                                  '<?php echo lang('Device_TableHead_LastIP');?>',
                                  '<?php echo lang('Device_TableHead_MAC');?>',
                                  '<?php echo lang('Device_TableHead_Status');?>',
                                  '<?php echo lang('Device_TableHead_MAC_full');?>',
                                  '<?php echo lang('Device_TableHead_LastIPOrder');?>',
                                  '<?php echo lang('Device_TableHead_Rowid');?>'];

      html = '';
                                  
      for(index = 0; index < tableColumnOrder.length; index++)
      {
        html += '<th>' + headersDefaultOrder[tableColumnOrder[index]] + '</th>';
      }

      $('#tableDevices tr').html(html);   
      


      // get parameter value
      $.get('php/server/parameters.php?action=get&defaultValue=50&parameter='+ parTableRows, function(data) {
        var result = JSON.parse(data);

        result = parseInt(result, 10)

        if (Number.isInteger (result) ) {
            tableRows = result;  
        }

        // get parameter value
        $.get('php/server/parameters.php?action=get&defaultValue=[[3,"desc"],[0,"asc"]]&parameter='+ parTableOrder, function(data) {
          var result = JSON.parse(data);
          result = JSON.parse(result);

          

          if (Array.isArray (result) ) {
            tableOrder = result;
          }

          // Initialize components with parameters
          initializeDatatable();

          // query data
          getDevicesTotals();
          getDevicesList (deviceStatus);
        });
      });
    });
   });
}

// -----------------------------------------------------------------------------
var tableColumnHide = [];

function mapIndx(oldIndex)
{
  for(i=0;i<tableColumnOrder.length;i++)
  {
    if(tableColumnOrder[i] == oldIndex)
    {
      // console.log('newIndex')
      // console.log(i)
      return i;
    }
  }
}

// -----------------------------------------------------------------------------

function initializeDatatable () {
  for(i = 0; i < tableColumnOrder.length; i++)
  {    
    // hide this column if not in the tableColumnVisible variable
    if(tableColumnVisible.includes(tableColumnOrder[i]) == false)
    {
      tableColumnHide.push(mapIndx(tableColumnOrder[i]));
    }    
  }

  // If the device has a small width (mobile) only show name, ip, and status columns. 
  if (window.screen.width < 400) {        
    tableColumnHide = [11,12,13,1,2,4,5,6,7,9];
  } 
  // else {  
  //   // var tableColumnHide = [11, 12, 13];
  //   tableColumnHide = [11, 12, 13];
  // };
  var table=
  $('#tableDevices').DataTable({
    'paging'       : true,
    'lengthChange' : true,
    'lengthMenu'   : [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, '<?php echo lang('Device_Tablelenght_all');?>']],
    'searching'    : true,

    'ordering'     : true,
    'info'         : true,
    'autoWidth'    : false,

    // Parameters
    'pageLength'   : tableRows,
    'order'        : tableOrder,
    // 'order'       : [[3,'desc'], [0,'asc']],

    'columnDefs'   : [
      {visible:   false,         targets: tableColumnHide },      
      {className: 'text-center', targets: [mapIndx(3), mapIndx(4), mapIndx(9), mapIndx(10)] },      
      {width:     '80px',        targets: [mapIndx(6), mapIndx(7)] },      
      {width:     '30px',        targets: [mapIndx(10), mapIndx(13)] },      
      {orderData: [mapIndx(11)],          targets: mapIndx(8) },

      // Device Name
      {targets: [mapIndx(0)],
        'createdCell': function (td, cellData, rowData, row, col) {
            $(td).html ('<b><a href="deviceDetails.php?mac='+ rowData[mapIndx(11)] +'" class="">'+ cellData +'</a></b>');
      } },

      // Icon      
      {targets: [mapIndx(3)],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (!emptyArr.includes(cellData)){
            $(td).html ('<i class="fa fa-'+cellData+' " style="font-size:16px"></i>');
          } else {
            $(td).html ('');
          }
      } },
      // Favorite      
      {targets: [mapIndx(4)],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (cellData == 1){
            $(td).html ('<i class="fa fa-star text-yellow" style="font-size:16px"></i>');
          } else {
            $(td).html ('');
          }
      } },
        
      // Dates      
      {targets: [mapIndx(6), mapIndx(7)],
        'createdCell': function (td, cellData, rowData, row, col) {
          $(td).html (translateHTMLcodes (cellData));
      } },

      // Random MAC      
      {targets: [mapIndx(9)],
        'createdCell': function (td, cellData, rowData, row, col) {
          if (cellData == 1){
            $(td).html ('<i data-toggle="tooltip" data-placement="right" title="Random MAC" style="font-size: 16px;" class="text-yellow glyphicon glyphicon-random"></i>');
          } else {
            $(td).html ('');
          }
      } },

      // Status color      
      {targets: [mapIndx(10)],
        'createdCell': function (td, cellData, rowData, row, col) {
          switch (cellData) {
            case 'Down':      color='red';              break;
            case 'New':       color='yellow';           break;
            case 'On-line':   color='green';            break;
            case 'Off-line':  color='gray text-white';  break;
            case 'Archived':  color='gray text-white';  break;
            default:          color='aqua';             break;
          };
      
          $(td).html ('<a href="deviceDetails.php?mac='+ rowData[10] +'" class="badge bg-'+ color +'">'+ cellData.replace('-', '') +'</a>');
      } },
    ],
    
    // Processing
    'processing'  : true,
    'language'    : {
      processing: '<table> <td width="130px" align="middle">Loading...</td><td><i class="ion ion-ios-loop-strong fa-spin fa-2x fa-fw"></td> </table>',
      emptyTable: 'No data',
      "lengthMenu": "<?php echo lang('Device_Tablelenght');?>",
      "search":     "<?php echo lang('Device_Searchbox');?>: ",
      "paginate": {
          "next":       "<?php echo lang('Device_Table_nav_next');?>",
          "previous":   "<?php echo lang('Device_Table_nav_prev');?>"
      },
      "info":           "<?php echo lang('Device_Table_info');?>",
    }
  });

  // Save cookie Rows displayed, and Parameters rows & order
  $('#tableDevices').on( 'length.dt', function ( e, settings, len ) {
    setParameter (parTableRows, len);
  } );
    
  $('#tableDevices').on( 'order.dt', function () {
    setParameter (parTableOrder, JSON.stringify (table.order()) );
    setCache ('devicesList', getDevicesFromTable(table) );
  } );

  $('#tableDevices').on( 'search.dt', function () {
    setCache ('devicesList', getDevicesFromTable(table) ); 
  } );

};


// -----------------------------------------------------------------------------
// Gets a JSON list of rowID and mac from the displayed table in the UI
function getDevicesFromTable(table)
{
  // rowIDs = table.column(12, { 'search': 'applied' }).data().toArray()  // rowID is in hidden column 12
  rowIDs = table.column(mapIndx(13), { 'search': 'applied' }).data().toArray()  // rowID is in hidden column 12
  // rowMACs = table.column(10, { 'search': 'applied' }).data().toArray() // MAC is in hidden column 10
  rowMACs = table.column(mapIndx(11), { 'search': 'applied' }).data().toArray() // MAC is in hidden column 10
  rowNames = table.column(mapIndx(0), { 'search': 'applied' }).data().toArray() // 
  rowTypes = table.column(mapIndx(2), { 'search': 'applied' }).data().toArray() // 

  result = []

  rowIDs.map(function(rowID, index){
    result.push({"rowid": rowID, "mac":rowMACs[index], "name" : rowNames[index],"type" : rowTypes[index] })
  })

  return JSON.stringify (result)
}

// -----------------------------------------------------------------------------
function getDevicesTotals () {
  // stop timer
  stopTimerRefreshData();

  // get totals and put in boxes
  $.get('php/server/devices.php?action=getDevicesTotals', function(data) {
    var totalsDevices = JSON.parse(data);

    $('#devicesAll').html        (totalsDevices[0].toLocaleString());
    $('#devicesConnected').html  (totalsDevices[1].toLocaleString());
    $('#devicesFavorites').html  (totalsDevices[2].toLocaleString());
    $('#devicesNew').html        (totalsDevices[3].toLocaleString());
    $('#devicesDown').html       (totalsDevices[4].toLocaleString());
    $('#devicesArchived').html   (totalsDevices[5].toLocaleString());

    // Timer for refresh data
    newTimerRefreshData (getDevicesTotals);
  } );
}


// -----------------------------------------------------------------------------
function getDeviceColumns () {

}
// -----------------------------------------------------------------------------
function getDevicesList (status) {
  // Save status selected
  deviceStatus = status;

  // Define color & title for the status selected
  switch (deviceStatus) {
    case 'all':        tableTitle = '<?php echo lang('Device_Shortcut_AllDevices');?>';  color = 'aqua';    break;
    case 'connected':  tableTitle = '<?php echo lang('Device_Shortcut_Connected');?>';   color = 'green';   break;
    case 'favorites':  tableTitle = '<?php echo lang('Device_Shortcut_Favorites');?>';   color = 'yellow';  break;
    case 'new':        tableTitle = '<?php echo lang('Device_Shortcut_NewDevices');?>';  color = 'yellow';  break;
    case 'down':       tableTitle = '<?php echo lang('Device_Shortcut_DownAlerts');?>';  color = 'red';     break;
    case 'archived':   tableTitle = '<?php echo lang('Device_Shortcut_Archived');?>';    color = 'gray';    break;
    default:           tableTitle = '<?php echo lang('Device_Shortcut_Devices');?>';     color = 'gray';    break;
  } 

  // Set title and color
  $('#tableDevicesTitle')[0].className = 'box-title text-'+ color;
  $('#tableDevicesBox')[0].className = 'box box-'+ color;
  $('#tableDevicesTitle').html (tableTitle);

  // Define new datasource URL and reload
  $('#tableDevices').DataTable().ajax.url(
    'php/server/devices.php?action=getDevicesList&status=' + deviceStatus).load();
};

</script>

<script src="js/pialert_common.js"></script>
