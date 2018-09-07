<?php
	$obj=new crud;

	$user = new user;
/*
	$user -> setRestrictions("viewT", "users", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "ugroup", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "ugroup", "", "ugroup", "1");
	$user -> setRestrictions("viewT", "languages", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "mainpages", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "news_source", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "category", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "brand", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "users", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "ugroup", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "languages", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "mainpages", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "news_source", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "category", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "brand", "", "ugroup", "4");

	$user -> setRestrictions("viewT", "ads_ad", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "ads_campaign", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "ads_clicks", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "ads_clients", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "ads_type", "", "ugroup", "4");
	$user -> setRestrictions("viewT", "ads_ad", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "ads_campaign", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "ads_clicks", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "ads_clients", "", "ugroup", "3");
	$user -> setRestrictions("viewT", "ads_type", "", "ugroup", "3");

	$user -> setRestrictions("updateT", "posts", "", "ugroup", "4");
	$user -> setRestrictions("delete", "posts", "", "ugroup", "3");
	$user -> setRestrictions("delete", "posts", "", "ugroup", "4");
	$user -> setRestrictions("limitA", "posts", "author");
*/
	$obj->addTable("users", "Users", "Users", "Administration");
	$obj->addColumn("users", "id", "ID", "", "pk");
	$obj->addColumn("users", "ugroup", "Group", "", "combo");
	$obj->addColumn("users", "username", "Username", "", "text");
	$obj->addColumn("users", "password", "Password", "", "password");
	$obj->addColumn("users", "name", "Name", "", "text");
	$obj->addColumn("users", "email", "E-mail", "", "text");
	$obj->addColumn("users", "telephone", "Telephone", "", "text");
		
	$obj->addTable("ugroup", "User groups", "User groups", "Administration");
	$obj->addColumn("ugroup", "id", "ID", "", "pk");
	$obj->addColumn("ugroup", "bdesc", "Description", "", "text");
	$obj->addRelation("ugroup", "id", "users", "ugroup", "bdesc");
	
	$obj->addTable("maindb", "Database", "Database", "main");
	$obj->addColumn("maindb", "id", "ID", "", "pk");
	$obj->addColumn("maindb", "visitorname", "Name", "", "text");
	$obj->addColumn("maindb", "email", "Email", "", "text");
	$obj->addColumn("maindb", "phone", "Phone", "", "text");
	$obj->addColumn("maindb", "reporttype", "Type", "", "combo");
	$obj->addColumn("maindb", "reportgroup", "Group", "", "combo");
	$obj->addColumn("maindb", "latitude", "Latitude", "", "text");
	$obj->addColumn("maindb", "longitude", "Longitude", "", "text");
	$obj->addColumn("maindb", "comments", "Comments", "", "text");
	$obj->addColumn("maindb", "posttime", "Posted", "", "date");
	$obj->addColumn("maindb", "publish", "Published", "", "check");
	$obj->addColumn("maindb", "image", "Image", "", "image");
	
	$obj->addTable("types", "Report types", "Report types", "Administration");
	$obj->addColumn("types", "id", "ID", "", "pk");
	$obj->addColumn("types", "bdesc", "Description", "", "text");
	$obj->addColumn("types", "publish", "Published", "", "check");
	$obj->addRelation("types", "id", "maindb", "reporttype", "bdesc");
	
	$obj->addTable("groups", "Report groups", "Report groups", "Administration");
	$obj->addColumn("groups", "id", "ID", "", "pk");
	$obj->addColumn("groups", "bdesc", "Description", "", "text");
	$obj->addColumn("groups", "publish", "Published", "", "check");
	$obj->addRelation("groups", "id", "maindb", "reportgroup", "bdesc");



/*
	$obj->addColumn("maindb", "lang", "Language", "", "combo");
	$obj->addColumn("maindb", "menuname", "Menu name", "", "text");
	$obj->addColumn("maindb", "maintitle", "Title", "", "text");
	$obj->addColumn("maindb", "seourl", "URL", "", "seo");
	$obj->addColumn("maindb", "fulltexta", "Text", "", "wysiwyg");

	$obj->addTable("languages", "Languages", "Languages", "Administration");
	$obj->addColumn("languages", "id", "ID", "", "pk");
	$obj->addColumn("languages", "bdesc", "Description", "", "text");
	
	$obj->addTable("mainpages", "Pages", "Pages", "main");
	$obj->addColumn("mainpages", "id", "ID", "", "pk");
	$obj->addColumn("mainpages", "lang", "Language", "", "combo");
	$obj->addColumn("mainpages", "menuname", "Menu name", "", "text");
	$obj->addColumn("mainpages", "maintitle", "Title", "", "text");
	$obj->addColumn("mainpages", "seourl", "URL", "", "seo");
	$obj->addColumn("mainpages", "fulltexta", "Text", "", "wysiwyg");
	$obj->addRelation("languages", "id", "mainpages", "lang", "bdesc");
	$obj->addOption("mainpages", "seourl", "1", "", "maintitle");	
	$obj->addOption("mainpages", "fulltexta","","","","col-sm-12");
	
	$obj->addTable("posts", "Articles", "Articles", "main");
	$obj->addColumn("posts", "id", "ID", "", "pk");
	$obj->addColumn("posts", "lang", "Language", "", "combo");
	$obj->addColumn("posts", "author", "Author", "", "combo");
	$obj->addColumn("posts", "category", "Category", "", "combo");
	$obj->addColumn("posts", "brand", "Brand", "", "combo");
	$obj->addColumn("posts", "posttime", "Time posted", "", "date");
	$obj->addColumn("posts", "news_source", "News source", "", "combo");
	$obj->addColumn("posts", "headline", "Headline", "", "text");
	$obj->addColumn("posts", "tags", "Tags", "", "tags");
	$obj->addColumn("posts", "counter", "Counter", "", "text");
	$obj->addColumn("posts", "description", "Description", "", "textarea");
	$obj->addColumn("posts", "fulltexta", "Text", "", "wysiwyg");
	$obj->addRelation("languages", "id", "posts", "lang", "bdesc");
	$obj->addRelation("category", "id", "posts", "category", "bdesc");
	$obj->addRelation("brand", "id", "posts", "brand", "bdesc");
	$obj->addRelation("users", "id", "posts", "author", "name");
	$obj->addOption("posts", "lang","1","1","","col-sm-2");
	$obj->addOption("posts", "author","1", $_SESSION['id'],"","hidden");
	$obj->addOption("posts", "category","1","","","col-sm-2");
	$obj->addOption("posts", "brand","1","0","","col-sm-2");
	$obj->addOption("posts", "headline","1","","","col-sm-6");
	$obj->addOption("posts", "posttime","",date("d.m.Y H:i:s"),"","col-sm-3");
	$obj->addOption("posts", "news_source","1","2","","col-sm-3");
	$obj->addOption("posts", "tags","","","","col-sm-6");
	$obj->addOption("posts", "counter","1","0","","col-sm-2");
	$obj->addOption("posts", "description","1","","","col-sm-10");
	$obj->addOption("posts", "fulltexta","","","","col-sm-12");
		
	$obj->addTable("news_source", "News source", "News source", "Administration");
	$obj->addColumn("news_source", "id", "ID", "", "pk");
	$obj->addColumn("news_source", "name", "Name", "", "text");
	$obj->addColumn("news_source", "url", "URL", "", "text");
	$obj->addRelation("news_source", "id", "posts", "news_source", "name");

	$obj->addTable("category", "Categories", "Categories", "Administration");
	$obj->addColumn("category", "id", "ID", "", "pk");
	$obj->addColumn("category", "lang", "Language", "", "combo");
	$obj->addColumn("category", "bdesc", "Description", "", "text");
	$obj->addColumn("category", "seourl", "URL", "", "seo");
	$obj->addColumn("category", "showonhome", "Show on Homepage", "", "check");
	$obj->addColumn("category", "showinmenu", "Show in Menu", "", "check");
	$obj->addColumn("category", "template", "Template", "", "text");
	$obj->addOption("category", "seourl","1","","bdesc");
	$obj->addOption("category", "template","","default");
	$obj->addRelation("languages", "id", "category", "lang", "bdesc");
	
	$obj->addTable("brand", "Brands", "Brands", "Administration");
	$obj->addColumn("brand", "id", "ID", "", "pk");
	$obj->addColumn("brand", "lang", "Language", "", "combo");
	$obj->addColumn("brand", "bdesc", "Description", "", "text");
	$obj->addColumn("brand", "seourl", "URL", "", "seo");
	$obj->addColumn("brand", "listonsite", "List on website", "", "check");
	$obj->addColumn("brand", "template", "Template", "", "text");
	$obj->addOption("brand", "seourl","1","","bdesc");
	$obj->addOption("brand", "template","","default");
	$obj->addRelation("languages", "id", "brand", "lang", "bdesc");
	
	$obj->addTable("ads_clients", "Clients", "Clients", "Marketing");
	$obj->addColumn("ads_clients", "id", "ID", "", "pk");
	$obj->addColumn("ads_clients", "name", "Name", "", "text");

	$obj->addTable("ads_campaign", "Campaign", "Campaign", "Marketing");
	$obj->addColumn("ads_campaign", "id", "ID", "", "pk");
	$obj->addColumn("ads_campaign", "client", "Client", "", "combo");
	$obj->addColumn("ads_campaign", "name", "Name", "", "text");
	$obj->addColumn("ads_campaign", "valid_from", "Valid from", "", "date");
	$obj->addColumn("ads_campaign", "valid_to", "Valid to", "", "date");
	$obj->addRelation("ads_clients", "id", "ads_campaign", "client", "name");

	$obj->addTable("ads_type", "Ads type", "Ads type", "Administration");
	$obj->addColumn("ads_type", "id", "ID", "", "pk");
	$obj->addColumn("ads_type", "bdesc", "Description", "", "text");

	$obj->addTable("ads_ad", "Ads", "Ads", "Marketing");
	$obj->addColumn("ads_ad", "id", "ID", "", "pk");
	$obj->addColumn("ads_ad", "campaign", "Campaign", "", "combo");
	$obj->addColumn("ads_ad", "ad_type", "Ad type", "", "combo");
	$obj->addColumn("ads_ad", "ad_file", "Image", "", "image");
	$obj->addColumn("ads_ad", "ad_html", "HTML (text)", "", "wysiwyg");
	$obj->addColumn("ads_ad", "point_to", "Link", "", "text");
	$obj->addColumn("ads_ad", "new_tab", "Open in new tab?", "", "check");
	$obj->addColumn("ads_ad", "shows", "Shows", "", "text");
	$obj->addColumn("ads_ad", "clicks", "Clicks", "", "text");
	$obj->addRelation("ads_campaign", "id", "ads_ad", "campaign", "name,valid_from,valid_to");
	$obj->addRelation("ads_type", "id", "ads_ad", "ad_type", "bdesc");
	$obj->addOption("ads_ad", "shows", "1", "0");	
	$obj->addOption("ads_ad", "clicks", "1", "0");	

	$obj->addTable("ads_clicks", "Clicks", "Clicks", "Marketing");
	$obj->addColumn("ads_clicks", "id", "ID", "", "pk");
	$obj->addColumn("ads_clicks", "ad", "Ad", "", "text");
	$obj->addColumn("ads_clicks", "time", "Time", "", "date");
	$obj->addColumn("ads_clicks", "ip", "IP", "", "text");
*/
?>