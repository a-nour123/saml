/*=========================================================================================
    File Name: chart-apex.js
    Description: Apexchart Examples
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

function drawDonutChart(selector, series, totalCount) {
  var donutChartEl = document.querySelector(selector),
    donutChartConfig = {
      states: {
        hover: {
          filter: {
            type: 'lighten',
            value: 0.01,
          }
        },
      },
      chart: {
        height: 350,
        type: 'donut'
      },
      legend: {
        show: true,
        position: 'bottom'
      },
      labels: [
        statuses['Not Applicable'].name,
        statuses['Not Implemented'].name,
        statuses['Partially Implemented'].name,
        statuses['Implemented'].name
      ],
      series: series,
      colors: [
        statuses['Not Applicable'].color,
        statuses['Not Implemented'].color,
        statuses['Partially Implemented'].color,
        statuses['Implemented'].color
      ],
      dataLabels: {
        enabled: true,
        style: {
          colors: [
            '#5e5873',
            '#5e5873',
            '#5e5873',
            '#5e5873'
          ]
        },
        background: {
          enabled: true,
          foreColor: '#f00',
          padding: 4,
          borderRadius: 5,
          borderWidth: 1,
          borderColor: '#fff',
          opacity: 0.9,
          dropShadow: {
            enabled: false,
            top: 1,
            left: 1,
            blur: 1,
            color: '#000',
            opacity: 0.45
          }
        },
        formatter: function (val, opt) {
          // return parseInt(val) + '%';
          return parseFloat(val.toFixed(2)) + '%';
        }
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              name: {
                fontSize: '2rem',
                fontFamily: 'Montserrat'
              },
              value: {
                fontSize: '1rem',
                fontFamily: 'Montserrat',
                formatter: function (val) {
                  // return parseInt((val / totalCount) * 100) + '%';
                  return parseFloat((val / totalCount) * 100).toFixed(2) + '%';
                }
              }
              // ,total: {
              //   show: true,
              //   fontSize: '1.5rem',
              //   label: statuses['Not Applicable'].name,
              //   formatter: function (w) {
              //     return parseInt((data['total']['Not Applicable'] / totalCount) * 100) + '%';
              //     // return parseFloat((data['total']['Not Applicable'] / totalCount) * 100).toFixed(2) + '%';
              //   }
              // }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 992,
          options: {
            chart: {
              height: 380
            }
          }
        },
        {
          breakpoint: 576,
          options: {
            chart: {
              height: 320
            },
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    name: {
                      fontSize: '1.5rem'
                    },
                    value: {
                      fontSize: '1rem'
                    },
                    total: {
                      fontSize: '1.5rem'
                    }
                  }
                }
              }
            }
          }
        }
      ]
    };
  if (typeof donutChartEl !== undefined && donutChartEl !== null) {
    var donutChart = new ApexCharts(donutChartEl, donutChartConfig);
    donutChart.render();
  }
}
$(function () {
  'use strict';


  // drawDonutChart(
  //   '#donut-chart-total',
  //   [
  //     data['total']['Not Applicable'],
  //     data['total']['Not Implemented'],
  //     data['total']['Partially Implemented'],
  //     data['total']['Implemented']
  //   ],
  //   data['all']
  // )

  // data['domains'].forEach(domain => {
  //   drawDonutChart(
  //     `#donut-chart-domain-${domain.id}`,
  //     [
  //       domain['Not Applicable'],
  //       domain['Not Implemented'],
  //       domain['Partially Implemented'],
  //       domain['Implemented']
  //     ],
  //     domain['total']
  //   )
  // });
});
