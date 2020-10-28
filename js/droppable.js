function makeDroppable(callback) {

  var element = document.querySelector('.droppable');

  let fileCount = 0;
  let fileList = [];

  let input = document.createElement('input');

  console.log(element.parent());

  let dropform = element.parent().find('.dropform'); // Where we need to append the input for submission
  let inputName = 'files[]';

  input.setAttribute('type', 'file');
  input.setAttribute('multiple', true);
  input.setAttribute('name', inputName);
  input.style.display = 'none';

  input.addEventListener('change', triggerCallback);
  dropform.appendChild(input);

  element.addEventListener('dragover', function (e) {
    e.preventDefault();
    e.stopPropagation();
    element.classList.add('dragover');
  });

  element.addEventListener('dragleave', function (e) {
    e.preventDefault();
    e.stopPropagation();
    element.classList.remove('dragover');
  });

  element.addEventListener('drop', function (e) {
    // console.log('Dropped item');
    e.preventDefault();
    e.stopPropagation();
    element.classList.remove('dragover');
    triggerCallback(e);
  });

  document.querySelector('.clear-files').addEventListener('click', function (e) {
    console.log('Clicked me', fileList);
    fileCount = 0;
    fileList = [];
    // HERE
    element.parent().find('.droppable_files').innerHTML = '';
    updateNumberText(fileCount);
    console.log('Clicked me', fileList);
  });


  document.querySelector('.ajax-upload').addEventListener('click', function(e){
    element.parent().find('.droppable_files').innerHTML = '';
    updateNumberText(0);
  });

  element.addEventListener('click', function () {
    input.value = null;
    input.click();
  });

  function triggerCallback(e) {
    var files;

    if (e.dataTransfer) {
      files = e.dataTransfer.files;
    } else if (e.target) {
      files = e.target.files;
    }

    fileList.push(files);
    console.log(fileList);
    element.parent().find('.droppable_files').innerHTML = '';

    fileCount += files.length;

    // --- Loop each of the uploaded images
    for (let i = 0; i < fileList.length; i++) {
      const files = fileList[i];
      for (let j = 0; j < files.length; j++) {
        createNewCell(files[j]);
      }
    }

    updateNumberText(fileCount);

    callback.call(null, createFormData(fileList));
  }

  function updateNumberText(count) {
    let input_count = element.find('.droppable_count');
    input_count.innerHTML = count;
  }


  function createNewCell(file) {

    let cellContainer = element.parent().find('.droppable_files');

    let wrapper = document.createElement('div');
    let fileIcon = document.createElement('div');
    let fileName = document.createElement('fileName');
    let fileSize = document.createElement('fileSize');

    let fileSizeInMB = round(file.size / 1000000, 2);

    fileSize.setAttribute('class', 'fileSize');
    fileName.setAttribute('class', 'fileName');
    fileIcon.setAttribute('class', 'fa fa-upload');
    wrapper.setAttribute('class', 'droppable_files_cell');


    fileName.innerHTML = file.name;
    fileSize.innerHTML = fileSizeInMB;


    wrapper.appendChild(fileIcon);
    wrapper.appendChild(fileName);
    wrapper.appendChild(fileSize);

    cellContainer.appendChild(wrapper);

  }

  return { files: createFormData(fileList) }

}