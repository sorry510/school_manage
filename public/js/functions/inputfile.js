$(function () {
  // 多文件上传
  $("input[type='file'][multiple]").on('filebatchselected', function (event, data, id, index) {
    $(this).fileinput('upload') // 自动上传
  });
  $("input[type='file'][multiple]")
    .parent()
    .parent()
    .parent()
    .parent()
    .find('.fileinput-remove')
    .css({ display: 'none' });  // 去掉右上角的删除
})
