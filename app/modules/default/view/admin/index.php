<html>
<head>
<link rel="stylesheet" type="text/css" href="<?= _js('ext-2.2.1/resources/css/ext-all.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?= _css('PurpleTheme/css/xtheme-purple.css'); ?>">
<script type="text/javascript" src="<?= _js('ext-2.2.1/adapter/ext/ext-base.js'); ?>"></script>
<script type="text/javascript" src="<?= _js('ext-2.2.1/ext-all.js'); ?>"></script>
<script type="text/javascript" src="<?= _js('extjs_plugins/pagesize.js'); ?>"></script>
<script type="text/javascript">

var pageSize = 20;

Ext.onReady(function(){
	
	//Ext.QuickTips.init();
	
	/**
	 * Treepanel de gauche
	 */
	var treePanel = new Ext.tree.TreePanel({
    	id: 'tree-panel',
    	title: 'Liste des mod&egrave;les',
        region:'center',
        split: true,
        layout:'fit',
        minSize: 150,
        autoScroll: true,
        
        // tree-specific configs:
        rootVisible: true,
        singleExpand: false,
        useArrows: true,
        animate:true,
        enableDD:false,
        
        dataUrl: '<?= _u(array('controller'=>'admin','action'=>'getModelList')); ?>',
        root: {
            nodeType: 'async',
            text: 'Applications',
            draggable:false,
            id:'source'
        }
    });
    

    /*var store = new Ext.data.JsonStore({
        url: '<?= _u(array('app'=>'admin','controller'=>'admin','action'=>'getDataGrid', 'model'=>'forum/forum')); ?>',
    	remoteSort: true
    });*/
    
    var pagingBar = new Ext.PagingToolbar({
        pageSize: pageSize,
        store: new Ext.data.Store(),//store,
        displayInfo: true,
        border:false,
        plugins:[new Ext.ux.Andrie.pPageSize],
        displayMsg: 'Displaying topics {0} - {1} of {2}',
        emptyMsg: "No topics to display"
    });
    

    var grid = new Ext.grid.GridPanel({
        id:'main-grid',
        store: new Ext.data.Store(),//store,
        //region:'center',
        //margins:'5 0 0 0',
        colModel: new Ext.grid.ColumnModel({}),
        selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),
        viewConfig: {forceFit:true},
        //title:'Donn&eacute;es',
        bbar: pagingBar,
        loadMask: true,
        border:false
    });
    
    /*store.on('metachange', function(store, meta) {
		if(meta.columns) {
			this.colModel.setConfig(meta.columns);
		}
	}, grid);*/
	
	/**
	 * Les events
	 */
	treePanel.on('click', function(n, e) {

    	if(n.leaf) {  // ignore clicks on folders and currently selected node 
    		var store = new Ext.data.JsonStore({
		        url: '<?= _u(array('controller'=>'admin','action'=>'getDataGrid')); ?>?model='+n.id,
		        remoteSort: true
		    });
	    	store.on('metachange', function(store, meta) {
    			if(meta.columns) {
    				Ext.getCmp('main-grid').colModel.setConfig(meta.columns);
    			}
    			
    		}, this);
    		
    		// Effacement des onglets
        	var cmp;
        	while (cmp =  Ext.getCmp('assoc-tab').getComponent(0)) {
        		Ext.getCmp('assoc-tab').remove(cmp);
        	}
    		
    		Ext.getCmp('content-panel').layout.setActiveItem(0);
    	    Ext.getCmp('main-grid').reconfigure(store, new Ext.grid.ColumnModel([]));
    	    Ext.getCmp('main-grid').getBottomToolbar().bind(store);
    	    grid.selModel.purgeListeners();
    	    grid.selModel.on('rowselect', function (o, index_row, record) {
        		
        		// Effacement des onglets
            	var cmp;
            	while (cmp =  Ext.getCmp('assoc-tab').getComponent(0)) {
            		Ext.getCmp('assoc-tab').remove(cmp);
            	}
        		
        		var assoc_store = new Array();
        		var assoc_grid = new Array();
        		
            	Ext.each (store.reader.meta.associations, function(item, index, all) {

            		// Le store de cette dépendance
            		assoc_store[index] = new Ext.data.JsonStore({
                    	url: '<?= _u(array('controller'=>'admin','action'=>'getDataGrid')); ?>?model='+n.id+'/'+item.model+'/'+record.get('id'),
                        //remoteSort: true
                    });
                    
                    // Le grid de la dépendance
                    assoc_grid[index] = new Ext.grid.GridPanel({
                        id:'assoc-grid-'+index,
                        title: item.model,
                        border:false,
                        autoDestroy:true,
                        store: assoc_store[index],
                        colModel: new Ext.grid.ColumnModel({}),
                        viewConfig: {forceFit:true},
                        loadMask: true
                    });
                    
                    // Chargement des colonnes de la dépendance
                    assoc_store[index].on('metachange', function(assocstore, meta) {
            			if(meta.columns) {
            				//alert('model : '+item.model);
            				Ext.getCmp('assoc-grid-'+index).colModel.setConfig(meta.columns);
            			}
            			
            		}, this);
            		
            		assoc_store[index].on('load', function() {
            			Ext.getCmp('assoc-tab').add(assoc_grid[index]);
            			//Ext.getCmp('assoc-tab').on('add', function() {
            				if (index == 0) {
	            				Ext.getCmp('assoc-tab').setActiveTab(assoc_grid[index]);
	            			}
	            		//});
            		}, this);
            		
            		assoc_store[index].load();
            		
            		// L'onglet !
            		//Ext.getCmp('assoc-tab').add(assoc_grid[index]);
            		
            		// On active le 1er onglet
            		/*if (index == 0) {
            			Ext.getCmp('assoc-tab').setActiveTab(assoc_grid);
            		}*/
            	});
            	Ext.getCmp('assoc-tab').doLayout();
            	Ext.getCmp('assoc-tab').setActiveTab('assoc-grid-0');
            }, this);
    	    store.load({params:{start:0, limit:pageSize}});
    	}
    }, this);
    
    /*grid.selModel.on('rowselect', function (o, index, record) {
    	//alert(record.get('name'));
    	Ext.each (store.reader.meta.associations, function(item, index, all) {
    		alert(item.toSource());
    	});
    	//alert(store.reader.meta.associations.toSource());
    	//document.write(store.toSource());
    });*/
    
    
	/**
	 * Vue gloable
	 */
	new Ext.Viewport({
		layout: 'border',
		title: 'Administration Framy',
		items: [{
			xtype: 'box',
			region: 'north',
			applyTo: 'header',
			height: 25
		},{
			layout: 'fit',
	    	id: 'tree-modele',
	        region:'west',
	        border: false,
	        split:true,
			margins: '0 0 5 5',
	        width: 175,
	        minSize: 100,
	        maxSize: 500,
			items: [treePanel]
		},{
			layout:'card',
			id: 'content-panel',
    		region: 'center',
    		margins: '0 5 5 0',
    		border: false,
    		activeItem:1,
    		items: [{
    			layout: 'border',
            	id: 'card-tabs-panel',
            	//plain: true,  //remove the header border
            	bodyBorder: false,
    			//title:'Mod&egrave;le',
    			split:true,
            	items: [{
                    items:grid,
                    layout:'fit',
                    region:'center',
                    title:'Donn&eacute;es',
                }, {
    			    title: 'Associations',
    			    region:'south',
    			    layout:'fit',
    			    collapsible: true,
    			    //margins: '5 0 0 0',
    			    //cmargins: '5 5 0 0',
    			    minSize:100,
                    height:250,
    			    //maxSize:800,
    			    split:true,
    			    items: [{
    			    	xtype: 'tabpanel',
    			    	id:'assoc-tab',
    			    	border:false,
						//activeTab: 0,
						enableTabScroll:true,
						layoutOnTabChange : true,
						//tabPosition:'bottom',
						
						/*items:[/*{
                            title: 'Tab 1',
                            html: 'This is tab 1 content.'
                        }/*,{
                            title: 'Tab 2',
                            html: 'This is tab 2 content.'
                        }]*/
    			    }]
    			}]
    		}, {
    			layout:'fit',
    			title:'Bienvenue',
    			id:'welcome-panel',
    			split:true
    		}]
        }],
        renderTo: Ext.getBody()
    });
    
    //store.load({params:{start:0, limit:pageSize}});
   	treePanel.expandAll();
   	
   	// Test scroll onglet
   	/*var index = 0;
    while(index < 27){
        Ext.getCmp('card-tabs-panel').add({
            title: 'New Tab ' + (++index),
            html: 'Tab Body ' + (index) + '<br/><br/>'
        }).show();
    }*/
   	
});
</script>
</head>
<body>
<div id="header"><h1>Module d'administration</h1></div>
</body>
</html>