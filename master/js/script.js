// Wait for the DOM to be fully loaded
d// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  // Get all the view links
  var view


  // Attach click event listeners to view links
  viewLinks.forEach(function(link) {
    link.addEventListener('click', function(event) {
      event.preventDefault();
      // Perform the view action
      performViewAction();
    });
  });

  // Get all the edit links
  var editLinks = document.querySelectorAll('.edit');

  // Attach click event listeners to edit links
  editLinks.forEach(function(link) {
    link.addEventListener('click', function(event) {
      event.preventDefault();
      // Perform the edit action
      performEditAction();
    });
  });

  // Get all the ping links
  var pingLinks = document.querySelectorAll('.ping');

  // Attach click event listeners to ping links
  pingLinks.forEach(function(link) {
    link.addEventListener('click', function(event) {
      event.preventDefault();
      var lanGateway = this.getAttribute('href').split('=').pop();
      // Perform the ping action
      performPingAction(lanGateway);
    });
  });

  // Function to perform the view action
  function performViewAction() {
    // Add your code here for the view action
    console.log('Performing View action');
  }

  // Function to perform the edit action
  function performEditAction() {
    // Add your code here for the edit action
    console.log('Performing Edit action');
  }

  // Function to perform the ping action
  function performPingAction(lanGateway) {
    // Add your code here for the ping action
    console.log('Performing Ping action for LAN Gateway: ' + lanGateway);
  }
});
