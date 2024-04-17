function updateStorageLevel() {
  fetch("/getStorageLevel")
  .then(response => {
      if (response.ok) {
          return response.json();
      } else {
          throw new Error('Failed to fetch storage level');
      }
  })
  .then(data => {
      const storageLevel = data.message;
      displaySVG(storageLevel);
  })
  .catch(error => {
      console.error('Error:', error);
      alert("An error occurred while fetching the storage level. Please try again later.");
  });
}

function displaySVG(storageLevel) {
  var svgContainer = document.getElementById("svg-container");
  var svgPath = "svg/battery" + storageLevel + ".svg";
  svgContainer.src = svgPath;
} 

updateStorageLevel();

setInterval(updateStorageLevel, 15000);