{"version":3,"file":"script.js","sources":["script.js"],"names":["BxInterfaceGrid","table_id","this","oActions","oColsMeta","oColsNames","customNames","oEditData","oSaveData","oOptions","oVisibleCols","vars","menu","settingsMenu","filterMenu","checkBoxCount","bColsChanged","bViewsChanged","oFilterRows","activeRow","_this","InitTable","tbl","document","getElementById","rows","length","cells","i","nCols","j","cell","onmouseover","HighlightGutter","onmouseout","className","user_authorized","onbxdragstart","DragStart","onbxdragstop","DragStop","onbxdrag","Drag","onbxdraghout","jsDD","registerObject","onbxdestdraghover","DragHover","onbxdestdraghout","DragOut","onbxdestdragfinish","DragFinish","registerDest","n","row","checkbox","childNodes","tagName","toUpperCase","type","onclick","SelectRow","EnableActions","jsUtils","addEvent","OnClickRow","HighlightRow","oncontextmenu","OnRowContext","r","e","window","event","phpVars","opt_context_ctrl","ctrlKey","targetElement","target","srcElement","el","FindParentObject","col_menu","SEPARATOR","PopupHide","BX","hasClass","OnClose","replace","SaveColumns","menuItems","util","array_merge","SetItems","BuildItems","arScroll","GetWindowScrollPos","x","clientX","scrollLeft","y","clientY","scrollTop","pos","PopupShow","returnValue","preventDefault","ShowActionMenu","index","ShowMenu","on","table","parentNode","gutter","cellIndex","bSorted","indexOf","span","id","selCount","parseInt","innerHTML","checked","toString","checkAll","obj","disabled","SelectAllRows","bChecked","box","form","forms","bEnabled","IsActionEnabled","bEnabledEdit","apply","b","action","elAll","SwitchActionButtons","bShow","buttonsTd","td","FindNextSibling","style","display","ActionEdit","a","ids","Array","tr","denyEvent","row_id","value","col_id","editable","data","name","appendChild","create","props","defaultChecked","options","list_val","items","selected","text","children","whiteSpace","size","href","title","mess","calend_title","html","calendar_image","server_time","maxlength","maxLength","elements","ActionCancel","allowEvent","ActionDelete","submit","DeleteItem","field_id","message","confirm","ForAllClick","for_all_confirm","Sort","url","by","sort_state","def_order","args","order","bControl","ajax","get","component_path","sessid","Reload","InitVisCols","CheckColumn","column","menuItem","colMenu","GetMenuByItemId","GetItemInfo","ICON","SetItemIcon","HideColumn","ApplySaveColumns","columns","sCols","Disable","current_url","AJAX_ID","insertToNode","location","SetTheme","theme","loadCSS","template_path","themeMenu","SetAllItemsIcon","SetView","view_id","filter_id","views","saved_filter","func","filters","ApplyFilter","EditCurrentView","ShowSettings","current_view","SaveSettings","AddView","Math","round","random","view","viewsNewView","sort_by","sort_order","page_size","custom_names","views_list","Option","viewsNoName","EditView","selectedIndex","DeleteView","viewsDelete","remove","bCreated","CDialog","content","settingsTitle","width","settingWndSize","height","resize_id","ClearButtons","SetButtons","settingsSave","parentWindow","Close","prototype","btnCancel","Show","view_name","focus","aVisCols","split","oVisCols","jsSelectUtils","deleteAllOptions","view_all_cols","colName","view_cols","selectOption","view_sort_by","view_sort_order","view_page_size","view_filters","viewsFilter","set_default_settings","delete_users_settings","up_btn","down_btn","rename_btn","add_btn","del_btn","RenameColumn","renameTitle","renameWndSize","buttons","selectedCol","settingsForm","col_name","col_name_def","doReload","GRID_ID","handler","post","ReloadViews","ShowViews","applyBtn","CWindowButton","viewsApply","hint","viewsApplyTitle","viewsTitle","btnClose","viewsWndSize","addCustomEvent","div","body","createElement","position","zIndex","clientWidth","__dragCopyDiv","arrowDiv","__dragArrowDiv","left","top","removeChild","obDrag","GetRealPos","delta","cols","index_from","index_to","tmp","InitFilter","SwitchFilterRow","mnu","MENU","ID","ICONCLASS","SwitchFilter","SaveFilterRows","SwitchFilterRows","sRows","filterShow","filterHide","ClearFilter","toLowerCase","l","onchange","onCustomEvent","clear_filter","ShowFilters","filtersApply","filtersApplyTitle","filters_list","filtersTitle","filtersWndSize","AddFilter","fields","filter","filtersNew","ShowFilterSettings","SaveFilter","insertIntoArray","mnuItem","TEXT","htmlspecialchars","TITLE","ApplyTitle","ONCLICK","AddFilterAs","GetFilterFields","EditFilter","DeleteFilter","filtersDelete","deleteFromArray","filterSettingsTitle","filterSettingWndSize","filter_name","SetFilterFields","val","substr","bWasSelected","sel","OnDateChange","bShowFrom","bShowTo","bShowHellip","bShowDays","bShowBr","findNextSibling","tag","class"],"mappings":"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"}