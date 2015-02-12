function define_sel_master() {
     if (document.selection)
     parent.document.getElementById('define').innerHTML = '<script type="text/javascript">selection = document.selection.createRange(); sel_master = true;'; //<script>
}
