<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>
<!-- JQuery min js -->
<script src="{{URL::asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap Bundle js -->
<script src="{{URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Ionicons js -->
<script src="{{URL::asset('assets/plugins/ionicons/ionicons.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/moment/moment.js')}}"></script>

<!-- Rating js-->
<script src="{{URL::asset('assets/plugins/rating/jquery.rating-stars.js')}}"></script>
<script src="{{URL::asset('assets/plugins/rating/jquery.barrating.js')}}"></script>
<script src="{{URL::asset('assets/js/eva-icons.min.js')}}"></script>
@yield('js')
<!-- Sticky js -->
<script src="{{URL::asset('assets/js/sticky.js')}}"></script>
<script src="{{URL::asset('assets/js/bootstrap-select.js')}}"></script>
<!-- custom js -->
<script src="{{URL::asset('assets/js/custom.js')}}"></script><!-- Left-menu js-->
<script src="{{URL::asset('assets/plugins/side-menu/sidemenu.js')}}"></script>
<!-- datatables js -->
<script src="{{URL::asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-buttons/js/buttons.print.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script> 
@php
    $datatableLanguage = __('datatable');
@endphp
<script>
    (function ($) {
        if (!$.fn || !$.fn.dataTable) {
            return;
        }
        var lang = @json($datatableLanguage);
        window.appDataTableLang = lang;
        var languageOptions = {
            decimal: lang.decimal || '',
            emptyTable: lang.emptyTable || '',
            info: lang.info || '',
            infoEmpty: lang.infoEmpty || '',
            infoFiltered: lang.infoFiltered || '',
            infoPostFix: lang.infoPostFix || '',
            thousands: lang.thousands || ',',
            lengthMenu: lang.lengthMenu || '',
            loadingRecords: lang.loadingRecords || '',
            processing: lang.processing || '',
            search: lang.search || '',
            searchPlaceholder: lang.searchPlaceholder || '',
            zeroRecords: lang.zeroRecords || '',
            paginate: lang.paginate || {},
            aria: lang.aria || {}
        };
        $.extend(true, $.fn.dataTable.defaults, {
            language: languageOptions
        });
        if ($.fn.dataTable.ext && $.fn.dataTable.ext.buttons && lang.buttons) {
            var buttons = $.fn.dataTable.ext.buttons;
            if (buttons.copy && lang.buttons.copy) {
                buttons.copy.text = lang.buttons.copy;
                buttons.copy.titleAttr = lang.buttons.copy;
            }
            if (buttons.excel && lang.buttons.excel) {
                buttons.excel.text = lang.buttons.excel;
                buttons.excel.titleAttr = lang.buttons.excel;
            }
            if (buttons.print && lang.buttons.print) {
                buttons.print.text = lang.buttons.print;
                buttons.print.titleAttr = lang.buttons.print;
            }
            if (buttons.colvis && lang.buttons.colvis) {
                buttons.colvis.text = lang.buttons.colvis;
                buttons.colvis.titleAttr = lang.buttons.colvis;
            }
        }
    })(jQuery);
</script>
<script src="{{URL::asset('assets/plugins/select2/js/select2.full.min.js')}}"></script> 

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $('.js-example-basic-single').select2({
        placeholder: "اختر مما يلى"
    });
    $('.progress-pie-chart').each(function () {
        var $ppc = $(this),
            percent = parseInt($ppc.data('percent')),
            deg = 360 * percent / 100;
        if (percent > 50) {
            $ppc.addClass('gt-50');
        }
        if (percent <= 25) {
            $ppc.addClass('red');
        } else if (percent >= 25 && percent <= 90) {
            $ppc.addClass('orange');
        } else if (percent >= 90) {
            $ppc.addClass('green');
        }
        $ppc.find('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
        $ppc.find('.ppc-percents span').html('<cite>' + percent + '</cite>' + '%');
    });
</script>
<script>
 
 
$(document).ready( function () {
 
            $("#example1").DataTable({
                "responsive": true, "lengthChange": true, "autoWidth": false, 
                "buttons": ["copy", "excel", "print", "colvis",
				], 
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            $("#invoice").DataTable({
                "responsive": true, "lengthChange": true, "autoWidth": false, "ordering": false,
                "buttons": ["copy", "excel", "print", "colvis",
				], 
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            $('#example2').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true 
            }); 

            $('#sTable').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true 
            }); 

            $('#example3').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": false 
            });  

    
$("#btnFullScreen").click(function () {
    alert('yes')
    /*
  	  if($(this).attr('data-fullscreen') === 'true') {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.webkitExitFullscreen) { 
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }
      $(this).attr('data-fullscreen', '');
      $(this).attr('value', 'Full Screen');
    } else {
      var el = document.documentElement,
        rfs = el.requestFullscreen
          || el.webkitRequestFullScreen
          || el.mozRequestFullScreen
          || el.msRequestFullscreen
      ;
      rfs.call(el);
      $(this).attr('data-fullscreen', 'true');
      $(this).attr('value', 'Exit Fullscreen');
    }
    */
 });
 
			
        });

</script>
<script>
    (function () {
        const KEY_ACTIONS = {
            F2: 'add',
            F3: 'edit',
            F4: 'delete',
            F5: 'refresh',
            F6: 'save',
            F9: 'print'
        };

        const ACTION_SELECTORS = {
            add: ['[data-shortcut="add"]', '#createButton', '.btn-add', '.btn-primary[data-action="add"]'],
            edit: ['[data-shortcut="edit"]', '.btn-edit', '.editBtn', '.btn-info[data-action="edit"]'],
            delete: ['[data-shortcut="delete"]', '.deleteBtn', '.btn-delete', 'button[data-action="delete"]'],
            refresh: ['[data-shortcut="refresh"]', '.btn-refresh', '.fa-rotate'],
            save: ['[data-shortcut="save"]', 'form button[type="submit"]'],
            print: ['[data-shortcut="print"]', 'button[onclick*="print"]', '.btn-print']
        };

        function isVisible(el) {
            return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length) && !el.disabled;
        }

        function findTarget(action) {
            const selectors = ACTION_SELECTORS[action] || [];
            for (const selector of selectors) {
                const el = document.querySelector(selector);
                if (el && isVisible(el)) {
                    return el;
                }
            }
            return null;
        }

        function trigger(el) {
            if (el.tagName === 'A' || el.tagName === 'BUTTON') {
                el.click();
            } else {
                el.dispatchEvent(new Event('click', { bubbles: true }));
            }
        }

        document.addEventListener('keydown', function (event) {
            const action = KEY_ACTIONS[event.key];
            if (!action) {
                return;
            }

            const target = findTarget(action);

            if (target) {
                event.preventDefault();
                trigger(target);
                return;
            }

            if (action === 'refresh') {
                event.preventDefault();
                window.location.reload();
            } else if (action === 'print') {
                event.preventDefault();
                window.print();
            }
        });
    })();
</script>
