jQuery(document).ready(function () {
  "use strict";

  jQuery("#late_time").timepicker({ format: "h:MM TT" });
  jQuery("#start_time").timepicker({ format: "h:MM TT" });
  jQuery("#end_time").timepicker({ format: "h:MM TT" });
  jQuery("#strttime").timepicker({ format: "h:MM TT" });
  jQuery("#endtime").timepicker({ format: "h:MM TT" });
  jQuery("#lunchstart").timepicker({ format: "h:MM TT" });
  jQuery("#lunchend").timepicker({ format: "h:MM TT" });

  /* Color picker */
  jQuery(".color-field").wpColorPicker();

  var i = 2;
  jQuery(".add_name_fields").on("click", function (e) {
    e.preventDefault();
    var x = document.createElement("INPUT");
    x.setAttribute("type", "text");
    x.setAttribute("id", "r_name_" + i);
    x.setAttribute("class", "form-control r_name");
    x.setAttribute("name", "r_name[]");
    x.setAttribute("placeholder", "Name");
    document.getElementById("dynamic_email_fields").appendChild(x);

    var y = document.createElement("INPUT");
    y.setAttribute("email", "email");
    y.setAttribute("id", "r_email_" + i);
    y.setAttribute("class", "form-control r_email");
    y.setAttribute("name", "r_email[]");
    y.setAttribute("placeholder", "Email");
    document.getElementById("dynamic_email_fields").appendChild(y);
    i++;
  });

  jQuery(".remove_email_fields").on("click", function (e) {
    e.preventDefault();
    i--;
    jQuery("#r_name_" + i).remove();
    jQuery("#r_email_" + i).remove();
  });

  jQuery("#report_table").DataTable({
    paginate: false,
    dom: "Blfrtip",
    buttons: [
      {
        extend: "copyHtml5",
        exportOptions: {
          columns: [0, 1, ":visible"],
        },
      },
      {
        extend: "excelHtml5",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "csvHtml5",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        text: "TSV",
        extend: "csvHtml5",
        fieldSeparator: "\t",
        extension: ".tsv",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "pdfHtml5",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "print",
        exportOptions: {
          columns: ":visible",
        },
      },
      "colvis",
    ],
    columnDefs: [
      {
        targets: -1,
        visible: false,
      },
    ],
  });
});
