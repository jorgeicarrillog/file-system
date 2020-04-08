<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS --><!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

  <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />

    <title>Sistema de Archivos</title>
  </head>
  <body class="container">
    <h1>Sistema de Archivos</h1>
    <div class="row">
      <div class="col-xs-7">
        <div id="tree">
        </div>
      </div>
      <div class="col-xs-5">
        <div id="data" style="height: 130px;">
        <div class="content default" style="text-align: center; height: 130px; display: block;"></div>
      </div>
      </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://use.fontawesome.com/c0d4fac22f.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script src="{{asset('moment-with-locales.js')}}"></script>
    <script type="text/javascript">
      $(function () {
      moment.locale('es');
      $(window).resize(function () {
        var h = Math.max($(window).height() - 0, 420);
        $('#container, #data, #tree, #data .content').height(h).filter('.default');
      }).resize();

      $('#tree')
        .jstree({
          'core' : {
            'data' : {
              'url' : 'listed?operation=get_node',
              'data' : function (node) {
                return { 'id' : node.id };
              }
            },
            'check_callback' : function(o, n, p, i, m) {
              if(m && m.dnd && m.pos !== 'i') { return false; }
              if(o === "move_node" || o === "copy_node") {
                if(this.get_node(n).parent === this.get_node(p).id) { return false; }
              }
              return true;
            },
            'themes' : {
              'responsive' : false,
              'variant' : 'small',
              'stripes' : true
            }
          },
          'sort' : function(a, b) {
            return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
          },
          'contextmenu' : {
            'items' : function(node) {
              var tmp = $.jstree.defaults.contextmenu.items();
              delete tmp.create.action;
              tmp.create.label = "New";
              tmp.create.submenu = {
                "create_folder" : {
                  "separator_after" : true,
                  "label"       : "Folder",
                  "action"      : function (data) {
                    var inst = $.jstree.reference(data.reference),
                      obj = inst.get_node(data.reference);
                    inst.create_node(obj, { type : "default" }, "last", function (new_node) {
                      setTimeout(function () { inst.edit(new_node); },0);
                    });
                  }
                },
                "create_file" : {
                  "label"       : "File",
                  "action"      : function (data) {
                    var inst = $.jstree.reference(data.reference),
                      obj = inst.get_node(data.reference);
                    inst.create_node(obj, { type : "file" }, "last", function (new_node) {
                      setTimeout(function () { inst.edit(new_node); },0);
                    });
                  }
                }
              };
              if(this.get_type(node) === "file") {
                delete tmp.create;
              }
              return tmp;
            }
          },
          "types" : {
            "#" : {
              "max_children" : 1,
              "max_depth" : 4,
              "valid_children" : ["root"]
            },
            "default" : {
              "valid_children" : ["default","file"]
            },
            "file" : {
              "icon" : "glyphicon glyphicon-file",
              "valid_children" : []
            }
          },
          'unique' : {
            'duplicate' : function (name, counter) {
              return name + ' ' + counter;
            }
          },
          'plugins' : ['state','dnd','sort','types','contextmenu','unique']
        })
        .on('delete_node.jstree', function (e, data) {
          $.get('listed?operation=delete_node', { 'id' : data.node.id, 'type': data.node.type })
            .fail(function () {
              data.instance.refresh();
            });
        })
        .on('create_node.jstree', function (e, data) {
          $.get('listed?operation=create_node', { 'type' : data.node.type, 'id' : data.node.parent, 'text' : data.node.text })
            .done(function (d) {
              data.instance.set_id(data.node, d.id);
            })
            .fail(function () {
              data.instance.refresh();
            });
        })
        .on('rename_node.jstree', function (e, data) {
          $.get('listed?operation=rename_node', { 'id' : data.node.id, 'text' : data.text, 'type': data.node.type })
            .done(function (d) {
              data.instance.set_id(data.node, d.id);
            })
            .fail(function () {
              data.instance.refresh();
            });
        })
        .on('move_node.jstree', function (e, data) {
          $.get('listed?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent, 'type': data.node.type })
            .done(function (d) {
              //data.instance.load_node(data.parent);
              data.instance.refresh();
            })
            .fail(function () {
              data.instance.refresh();
            });
        })
        .on('copy_node.jstree', function (e, data) {
          $.get('listed?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent, 'type': data.node.type })
            .done(function (d) {
              //data.instance.load_node(data.parent);
              data.instance.refresh();
            })
            .fail(function () {
              data.instance.refresh();
            });
        })
        .on('changed.jstree', function (e, data) {
          if(data && data.selected && data.selected.length) {
            $.get('listed?operation=get_content&type='+data.node.type+'&id=' + data.selected.join(':'), function (d) {
            console.log(d);
              if(d && typeof d.type !== 'undefined') {
                switch(d.type) {
                  case 'file':
                    html = '<p><i class="glyphicon glyphicon-file" role="presentation"></i>'
                           +'Nombre: '+d.text
                           +'<br>Creado: '+moment(d.created_at).format('MMMM Do YYYY, h:mm:ss a')
                           +'<br>Actualizado: '+moment(d.updated_at).format('MMMM Do YYYY, h:mm:ss a')
                           +'</p>';
                    $('#data .default').html(html).show();
                    break;
                  case 'folder':
                    $('#data .default').html(d.text).show();
                    break;
                  default:
                    $('#data .default').html(d.text).show();
                    break;
                }
              }else{
                $('#data .default').html('Select a file from the tree.').show();
              }
            });
          }
          else {
            $('#data .default').html('Select a file from the tree.').show();
          }
        });
    });
    </script>
  </body>
</html>