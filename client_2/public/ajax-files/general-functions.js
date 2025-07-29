
function GetColors(){
    const color=[[
        '#6FB2D2',
        '#85C88A',
        '#EBD671',
        '#DBDFFD'
    ],[
        '#557B83',
        '#39AEA9',
        '#A2D5AB',
        '#E5EFC1'
    ],[
        '#733C3C',
        '#8479E1',
        '#B4ECE3',
        '#FFF8D5'
    ],[
        '#5F7161',
        '#6D8B74',
        '#EFEAD8',
        '#D0C9C0'
    ],[
        '#E9D5CA',
        '#827397',
        '#4D4C7D',
        '#363062'
    ],[
        '#7C3E66',
        '#F2EBE9',
        '#A5BECC',
        '#243A73'
    ],[
        '#809A6F',
        '#A25B5B',
        '#CC9C75',
        '#D5D8B5'
    ]];
    const colorSort = color.sort((a, b) => 0.5 - Math.random());
    return [].concat(colorSort[0],colorSort[1],colorSort[2],colorSort[3],colorSort[4],colorSort[5],colorSort[6] );

}

if (jQuery.fn.select2) {
    $('select.multiple-select2').select2();
    $('select.select2').select2();
  }
