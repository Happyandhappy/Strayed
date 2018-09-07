$(document).on('hidden.bs.modal', '.modal', function () {
	$('.modal:visible').length && $(document.body).addClass('modal-open');
});
$(document).on('summernote.blur', '#fulltexta', function () {
if (getUrlParameter("table")=="posts"){
	localStorage.setItem("lastText", $('#fulltexta').summernote('code'));
}
});
$(document).on('shown.bs.modal', '.addnewmodal', function () {
if (getUrlParameter("table")=="posts" && localStorage.getItem("lastText")){
	if (confirm("Do you want to load last unsaved text?") == true) {
		$('#fulltexta').summernote('code',localStorage.getItem("lastText"));
		localStorage.removeItem("lastText");
	}else{localStorage.removeItem("lastText");}
}
});

$(document).ready(function () {
	//$('.panel').lobiPanel();

	$('#defaultNavbar1').append('<ul class="nav navbar-nav navbar-right"><li><a href="#" onclick="toggleFull()"><span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span></a></li></ul>');
	$(".panel-heading h3").append('<div class="dugmici"></div>');
	$(window).scroll(function () {
		if ($(".fixedHeader-floating").length) {
			$(".panel-heading").css("position", "fixed");
		} else {
			$(".panel-heading").css("position", "inherit");
		}
	});

	$('#glavnatabela tbody').on('click', 'tr', function () {
		if ($(this).hasClass('selected')) {
			//console.log("jesam");
		}
	});

	var attachFastClick = Origami.fastclick;
	attachFastClick(document.body);

	var rr = 0;
	$('#glavnatabela tfoot th').each(function () {
		if (getUrlParameter("filtert")) return;
		var ajde = function (rr) {
			return function (responseTxt, statusTxt, xhr) {
				if (statusTxt == "success") {
					$(this).find(".selectpickera").attr("broj", rr);
					$(".selectpickera", this).on('change', function (rr, opa) {
						table.column($(this).closest("th").index() + ':visible').search(this.value).draw();
					});
				}
			};
		};

		var title = $(this).text();
		var opa = this;

		if ($(this).hasClass("select-filter")) {
			$(this).load("./?ajax=getcombo&tab={{tabela}}&kol=" + $(this).attr("col-name"), ajde(rr));
		}
		if ($(this).hasClass("datetimepicker")) {
			$(this).html('<input class="form-control" broj="' + rr + '" type="text" placeholder="' + title + '" />');
			$(":input", this).datetimepicker();
			$('input', this).on('blur', function () {
				table.column($(this).parent("th").index() + ':visible').search(this.value).draw();
			});
		}
		if ($(this).hasClass("normalan")) {
			$(this).html('<input class="form-control" broj="' + rr + '" type="text" placeholder="' + title + '" />');
			$('input', this).on('blur', function () {
				table.column($(this).parent("th").index() + ':visible').search(this.value).draw();
			});
		}
		rr = rr + 1;
	});


	var table;
	createTable();

	function createTable(maintable = "#glavnatabela") {
		if (getUrlParameter('filtert')) {
			var nesto = "./?ajax=gettabledata&table={{tabela}}" + "&filtert=" + getUrlParameter('filtert') + "&filterc=" + getUrlParameter('filterc') + "&filterid=" + getUrlParameter('filterid');
		} else {
			var nesto = "./?ajax=gettabledata&table={{tabela}}";
		}
		if (getUrlParameter('table') == "ino1" || getUrlParameter('table') == "nereseni") { var doma = '<"pull-right"B>rtip'; } else { var doma = 'rtp'; }
		var doma = '<"pull-right"B>rtip';
		table = $(maintable).DataTable({
			language: {
				"url": "langs/" + locale + ".json"
			},
			processing: true,
			serverSide: true,
			ajax: nesto,
			columns: [{{kolonezajs}}],
			rowId: 'id',
			lengthChange: false,
			order:[[0, "desc"]],
			dom: doma,
			buttons: [
				{
					extend: 'collection',
					className: 'btn-xs',
					text: p('Izvoz'),
					buttons: ['copy', 'excel', 'pdf', { extend: 'print', text: p('Štampa') },]
				},
				{ extend: 'pageLength', className: 'btn-xs', text: p('Broj zapisa') },
				{ extend: 'colvis', className: 'btn-xs', text: p('Kolone') },
				'clearstate',
			],
			fixedHeader: true,
			colReorder: true,
			responsive: false,
			stateSave: true,
			select: {
				style: 'single',
				info: false,
			},
			"initComplete": function (settings, json) {
				$(document).ajaxStop(function () {
					$("[type=\'checkbox\']").bootstrapSwitch({ "size": "mini" });
					$('.selectpickera').selectpicker({
						liveSearch: true,
						title: p("Odaberi"),
						size: false
					});
				});
			},

		});

$.fn.dataTable.ext.buttons.clearstate = {
	text: p('Resetuj prikaz'),
	className: "btn-warning btn-xs",
	action: function (e, dt, node, config) {
		table.colReorder.reset();
		table.state.clear();
		table.destroy();
		createTable();
	}
};

new $.fn.dataTable.Buttons(table, {
	buttons: [
		{
			text: p('Dodaj'),
			className: "btn-primary btn-xs",
			action: function (e, dt, node, config) {
				NProgress.configure({ parent: '.addnewmodal .modal-body' });
				NProgress.start();
				link = "./?show=insert&table={{tabela}}&filtert=" + getUrlParameter('filtert') + "&filterc=" + getUrlParameter('filterc') + "&filterid=" + getUrlParameter('filterid');
				$(".editmodal .modal-body").html("");
				$(".addnewmodal .modal-body").html("");
				$(".duplmodal .modal-body").html("");
				$(".addnewmodal").modal({ backdrop: "static" });
				$(".addnewmodal .modal-body").load(link, function (responseTxt, statusTxt, xhr) {
					if (statusTxt == "success") {
						NProgress.done();
						NProgress.configure({ parent: 'body' });
						$("#addnewform").validator();
						enter2tab("#addnewform");
					}
					if (statusTxt == "error") {
						NProgress.done();
						NProgress.configure({ parent: 'body' });
						alert("Error: " + xhr.status + ": " + xhr.statusText);
					}
				});
			}
		},
		{
			extend: 'selectedSingle',
			text: p('Duplikat'),
			className: "btn-info btn-xs dupldugme",
			action: function (e, dt, node, config) {
				var selectedid = table.rows({ selected: true }).data()[0].id;
				NProgress.configure({ parent: '.duplmodal .modal-body' });
				NProgress.start();
				link = './?show=update&table={{tabela}}&id=' + selectedid;
				$(".addnewmodal .modal-body").html("");
				$(".editmodal .modal-body").html("");
				$(".duplmodal .modal-body").html("");
				$(".duplmodal").modal({ backdrop: "static" });
				$(".duplmodal .modal-body").load(link, function (responseTxt, statusTxt, xhr) {
					if (statusTxt == "success") {
						$("#function").val("insert");
						NProgress.done();
						NProgress.configure({ parent: 'body' });
						enter2tab();
						$("#editform").validator();
						enter2tab("#editform");
					}
					if (statusTxt == "error") {
						NProgress.done();
						NProgress.configure({ parent: 'body' });
						alert("Error: " + xhr.status + ": " + xhr.statusText);
					}
				});
			}
		},
		{
			extend: 'selectedSingle',
			text: p('Izmeni'),
			className: "btn-warning btn-xs izmenidugme",
			action: function (e, dt, node, config) {
				var selectedid = table.rows({ selected: true }).data()[0].id;
				if($(table.rows({ selected: true }).data()[0].locked).is(':checked')){alert(p("Ne možete menjati zaključan zapis!")); return;}
				NProgress.configure({ parent: '.editmodal .modal-body' });
				NProgress.start();
				link = './?show=update&table={{tabela}}&id=' + selectedid;
				$(".addnewmodal .modal-body").html("");
				$(".editmodal .modal-body").html("");
				$(".duplmodal .modal-body").html("");
				$(".editmodal").modal({ backdrop: "static" });
				$(".editmodal .modal-body").load(link, function (responseTxt, statusTxt, xhr) {
					if (statusTxt == "success") {
						NProgress.done();
						NProgress.configure({ parent: 'body' });
						enter2tab();
						$("#editform").validator();
						enter2tab("#editform");
					}
					if (statusTxt == "error") {
						NProgress.done();
						NProgress.configure({ parent: 'body' });
						alert("Error: " + xhr.status + ": " + xhr.statusText);
					}
				});
			}
		},
		{
			extend: 'selectedSingle',
			text: p('Obriši'),
			className: "btn-danger btn-xs brisidugme",
			action: function (e, dt, node, config) {
				if (confirm(p('Da li ste sigurni?'))) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					link = './?function=delete&table={{tabela}}&id=' + selectedid;
					var op;
					$('#prazan').load(link, function (responseTxt, statusTxt, xhr) {
						if (statusTxt == "success") {
							table.ajax.reload(null, false);
						}
						if (statusTxt == "error")
							alert("Error: " + xhr.status + ": " + xhr.statusText);
					});
				};
			}
		},
	]
});
table.buttons(0, null).container().prependTo(
	$(".dugmici")
);

if (getUrlParameter("table") == "nereseni") {//samo inost
	new $.fn.dataTable.Buttons(table, {
		buttons: [
			{
				extend: 'selectedSingle',
				text: p('Prikazi osiguranika'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].LBO;
					frameSrc = "./?show=table&table=ino1&filtert=ino1&filterc=lbo&filterid=" + selectedid;
					window.location.href = frameSrc;
				}
			},
		]
	})
	table.buttons(0, null).container().prependTo(
		$(".dugmici")
	);
}
if (getUrlParameter("table") == "documents") {//samo vorae
	new $.fn.dataTable.Buttons(table, {
		buttons: [
			{
				extend: 'selectedSingle',
				text: p('Komentari'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					frameSrc = "./?show=table&table=comments&filtert=comments&filterc=document&filterid=" + selectedid;
					window.location.href = frameSrc;
				}
			},
		]
	})
	table.buttons(0, null).container().prependTo(
		$(".dugmici")
	);
}
if (getUrlParameter("table") == "o125") {//samo inost
	new $.fn.dataTable.Buttons(table, {
		buttons: [
			{
				extend: 'selectedSingle',
				text: p('Detalji obracuna'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					$('#detalj .sadrzaj').html('');
					frameSrc = "./?show=qtable&table=o125izvestaj&filtert=o125izvestaj&filterc=o125&filterid=" + selectedid;
					$('#detalj .sadrzaj').append('<iframe src="' + frameSrc + '"></iframe>');
					$('#detalj').bPopup({});
				}
			},
			{
				extend: 'selectedSingle',
				text: p('Stampa obracuna'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					frameSrc = "./?print=o125&id=" + selectedid;
					$('#detalj .sadrzaj').html("");
					$('#detalj .sadrzaj').append('<iframe src="' + frameSrc + '"></iframe>');
					$('#detalj').bPopup().close();
				}
			},
		]
	})
	table.buttons(0, null).container().prependTo(
		$(".dugmici")
	);
}

if (getUrlParameter("table") == "postsa") {//samo autonet
	new $.fn.dataTable.Buttons(table, {
		buttons: [
			{
				extend: 'selectedSingle',
				text: p('Images'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					$('#detalj .sadrzaj').html('<div class="tabbable" id="tabovi"></div>');
					$('#tabovi').html('<ul class="nav nav-tabs"></ul><div class="tab-content"></div>');
					//Tab Images
					frameSrc = "./images.php?postid=" + selectedid;
					broj = Math.round(Math.random() * 100);
					$('#tabovi .nav').append('<li class="active"><a href="#panel-' + broj + '" data-toggle="tab">Images</a></li>');
					$('#tabovi .tab-content').append('<div class="tab-pane active" id="panel-' + broj + '"></div>');
					$('#panel-' + broj).append('<iframe src="' + frameSrc + '"></iframe>');

					$('#detalj').bPopup({});
				}
			},
		]
	})
	table.buttons(0, null).container().prependTo(
		$(".dugmici")
	);
}
//if (getUrlParameter("table") == "multimedia") {//samo autonet
if (getUrlParameter("table") == "posts") {//samo autonet
	new $.fn.dataTable.Buttons(table, {
		buttons: [
			{
				extend: 'selectedSingle',
				text: p('Files'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					$('#detalj .sadrzaj').html('<div class="tabbable" id="tabovi"></div>');
					$('#tabovi').html('<ul class="nav nav-tabs"></ul><div class="tab-content"></div>');
					//Tab Fajlovi
					frameSrc = "./gallery/index.php?postid=" + selectedid;
					broj = Math.round(Math.random() * 100);
					$('#tabovi .nav').append('<li class="active"><a href="#panel-' + broj + '" data-toggle="tab">Files</a></li>');
					$('#tabovi .tab-content').append('<div class="tab-pane active" id="panel-' + broj + '"></div>');
					$('#panel-' + broj).append('<iframe src="' + frameSrc + '"></iframe>');

					$('#detalj').bPopup({});
				}
			},
		]
	})
	table.buttons(0, null).container().prependTo(
		$(".dugmici")
	);
}

if (getUrlParameter("table") == "events") {//samo vorae
	new $.fn.dataTable.Buttons(table, {
		buttons: [
			{
				extend: 'selectedSingle',
				text: p('Detalji'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					$('#detalj .sadrzaj').html('<div class="tabbable" id="tabovi"></div>');
					$('#tabovi').html('<ul class="nav nav-tabs"></ul><div class="tab-content"></div>');
					//Tab members
					frameSrc = "./?show=qtable&table=members&filtert=members&filterc=event&filterid=" + selectedid;
					broj = Math.round(Math.random() * 100);
					$('#tabovi .nav').append('<li class="active"><a href="#panel-' + broj + '" data-toggle="tab">Members</a></li>');
					$('#tabovi .tab-content').append('<div class="tab-pane active" id="panel-' + broj + '"></div>');
					$('#panel-' + broj).append('<iframe src="' + frameSrc + '"></iframe>');
					//Tab parties
					frameSrc = "./?show=qtable&table=parties&filtert=parties&filterc=event&filterid=" + selectedid;
					broj = Math.round(Math.random() * 100);
					$('#tabovi .nav').append('<li class="activee"><a href="#panel-' + broj + '" data-toggle="tab">Parties</a></li>');
					$('#tabovi .tab-content').append('<div class="tab-pane activee" id="panel-' + broj + '"></div>');
					$('#panel-' + broj).append('<iframe src="' + frameSrc + '"></iframe>');
					//Tab equipment
					frameSrc = "./?show=qtable&table=equipments&filtert=equipments&filterc=event&filterid=" + selectedid;
					broj = Math.round(Math.random() * 100);
					$('#tabovi .nav').append('<li class="activee"><a href="#panel-' + broj + '" data-toggle="tab">Equipments</a></li>');
					$('#tabovi .tab-content').append('<div class="tab-pane activee" id="panel-' + broj + '"></div>');
					$('#panel-' + broj).append('<iframe src="' + frameSrc + '"></iframe>');

					$('#detalj').bPopup({});
				}
			},
		]
	})
	table.buttons(0, null).container().prependTo(
		$(".dugmici")
	);
/*	new $.fn.dataTable.Buttons(table, {
		buttons: [
			{
				extend: 'selectedSingle',
				text: p('Štampa INO1'),
				className: "btn btn-xs",
				action: function (e, dt, node, config) {
					var selectedid = table.rows({ selected: true }).data()[0].id;
					frameSrc = "./?print=ino1&id=" + selectedid;
					$('#detalj .sadrzaj').html("");
					$('#detalj .sadrzaj').append('<iframe src="' + frameSrc + '"></iframe>');
					$('#detalj').bPopup().close();
				}
			},
		]
	})
	table.buttons(0, null).container().prependTo(
		$(".dugmici")
	);*/
}

	}

table.on('processing', function (e, settings, processing) {
	processing ? NProgress.start() : NProgress.done();
	processing ? $("html").css("cursor", "wait") : $("html").css("cursor", "auto");
});

table.on('draw', function () {
	scrollTable();
	//--------Will check does image exist for article
    if (getUrlParameter("table") == "posts"){
    	$('#glavnatabela tbody tr td').each(function () {
            //console.log( table.cell( this ).data() );
            //console.log( table.cell( this ).index().row + " - " + table.cell( this ).index().column );
            var red=table.cell( this ).index().row;
            var kolona=table.cell( this ).index().column;
            var vrednost=table.cell( this ).data();
            if(kolona==0){
                link = './?ajax=doesimgexist&id=' + vrednost;
                var e = $('#prazan').load(link, function (responseTxt, statusTxt, xhr) {
                    if (statusTxt == "success") {
                        if(responseTxt!==""){
                            $("#"+responseTxt).addClass( 'noimage' );
                        }
                    }
                    if (statusTxt == "error")
                        alert("Error: " + xhr.status + ": " + xhr.statusText);
                });
            }
        });
    }
    //--------Will check does image exist for article
});

$('.panel').on('init.lobiPanel resizeStop.lobiPanel onSmallSize.lobiPanel onFullScreen.lobiPanel onMaximize.lobiPanel onPin.lobiPanel onUnpin.lobiPanel', function (ev, lobiPanel) {
	scrollTable();
});

function scrollTable() {
	var $div = $('.panel-body');
	var $ul = $('table');
	var width = $div.width();
	var ulWidth = $ul.width() - width;
	$div.on('mouseenter', function (e) {
		var divLeft = $div.offset().left;
		$('.panel').on('mousemove', function (e) {
			var left = e.pageX - divLeft;
			var percent = left / width;
			$ul.css('margin-left', -(percent * ulWidth));
			$(".fixedHeader-floating").css('margin-left', -(percent * ulWidth) + 15);
		});
	}).on('mouseleave', function () {
		$('.panel').off('mousemove');
	});
}

$("#addnewformsave").on("click", function (e) {
	e.preventDefault();
	var inputs = $('#addnewform *').filter(':input');
	var valid = true;
	$.each(inputs, function (key, val) {
		if (!val.validity.valid) {
			valid = false;
			return;
		}
	});
	if (valid) {
		NProgress.start();
		$.ajax({
			url: $("#addnewform").attr("action"),
			type: 'POST',
			data: new FormData($('form')[0]),
			cache: false,
			contentType: false,
			processData: false,
			xhr: function () {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) {
					$(".addnewmodal .modal-body").html('<progress value="0" max="100"></progress>');
					myXhr.upload.addEventListener('progress', function (e) {
						if (e.lengthComputable) {
							$('progress').attr({
								value: e.loaded,
								max: e.total,
							});
						}
					}, false);
				}
				return myXhr;
			},
		})
			.done(function (data) {
				$("body").append(data);
				$(".addnewmodal").modal("hide");
				table.ajax.reload(null, false);
				NProgress.done();
			});
	} else {
		$('#addnewform').validator('destroy');
		$('#addnewform').validator('validate');
	}
});
$("#editformsave").on("click", function (e) {
	e.preventDefault();
	var inputs = $('#editform *').filter(':input');
	var valid = true;
	$.each(inputs, function (key, val) {
		if (!val.validity.valid) {
			valid = false;
			return;
		}
	});
	if (valid) {
		NProgress.start();
		$.ajax({
			url: $("#editform").attr("action"),
			type: 'POST',
			data: new FormData($('form')[0]),
			cache: false,
			contentType: false,
			processData: false,
			xhr: function () {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) {
					$(".editmodal .modal-body").html('<progress value="0" max="100"></progress>');
					myXhr.upload.addEventListener('progress', function (e) {
						if (e.lengthComputable) {
							$('progress').attr({
								value: e.loaded,
								max: e.total,
							});
						}
					}, false);
				}
				return myXhr;
			},
		})
			.done(function (data) {
				$("body").append(data);
				$(".editmodal").modal("hide");
				table.ajax.reload(null, false);
				NProgress.done();
			});
	} else {
		$('#editform').validator('destroy');
		$('#editform').validator('validate');
	}
});
$("#duplformsave").on("click", function (e) {
	e.preventDefault();
	var inputs = $('#editform *').filter(':input');
	var valid = true;
	$.each(inputs, function (key, val) {
		if (!val.validity.valid) {
			valid = false;
			return;
		}
	});
	if (valid) {
		NProgress.start();
		$.ajax({
			url: $("#editform").attr("action"),
			type: 'POST',
			data: new FormData($('form')[0]),
			cache: false,
			contentType: false,
			processData: false,
			xhr: function () {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) {
					$(".duplmodal .modal-body").html('<progress value="0" max="100"></progress>');
					myXhr.upload.addEventListener('progress', function (e) {
						if (e.lengthComputable) {
							$('progress').attr({
								value: e.loaded,
								max: e.total,
							});
						}
					}, false);
				}
				return myXhr;
			},
		})
			.done(function (data) {
				$("body").append(data);
				$(".duplmodal").modal("hide");
				table.ajax.reload(null, false);
				NProgress.done();
			});
	} else {
		$('#editform').validator('destroy');
		$('#editform').validator('validate');
	}
});
});

function cancelFullScreen(el) {
	var requestMethod = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullscreen;
	if (requestMethod) {// cancel full screen.
		requestMethod.call(el);
	} else if (typeof window.ActiveXObject !== "undefined") {// Older IE.
		var wscript = new ActiveXObject("WScript.Shell");
		if (wscript !== null) {
			wscript.SendKeys("{F11}");
		}
	}
};
function requestFullScreen(el) {
	// Supports most browsers and their versions.
	var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullscreen;

	if (requestMethod) {// Native full screen.
		requestMethod.call(el);
	} else if (typeof window.ActiveXObject !== "undefined") {// Older IE.
		var wscript = new ActiveXObject("WScript.Shell");
		if (wscript !== null) {
			wscript.SendKeys("{F11}");
		}
	}
	return false;
};
function toggleFull() {
	var elem = document.documentElement;
	// Make the body go full screen.
	var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) || (document.mozFullScreen || document.webkitIsFullScreen);

	if (isInFullScreen) {
		cancelFullScreen(document);
	} else {
		requestFullScreen(elem);
	}
	return false;
};
function enter2tab(forma) {
	$(forma + " :input").keydown(function (event) {
		if (event.keyCode == 13) {
			event.preventDefault();
			var currentInput = this;
			var isOnCurrent = false;
			$('.form-control').each(function () {
				if (isOnCurrent == true) {
					$(this).focus();
					return false;
				}
				if (this == currentInput) {
					isOnCurrent = true;
				}
			});
		}
	});
};
var getUrlParameter = function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
};