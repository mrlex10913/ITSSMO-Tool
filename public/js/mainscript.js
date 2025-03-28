document.getElementById('generate-btn').addEventListener('click', function() {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const tableBody = document.getElementById('barcode-table-body');

    // Clear existing rows
    tableBody.innerHTML = '';

    // Generate the specified number of rows
    for (let i = 1; i <= quantity; i++) {
      const row = document.createElement('tr');
      row.className = 'border-t border-gray-800';

      // Create barcode number cell
      const numberCell = document.createElement('td');
      numberCell.className = 'px-6 py-4';
      numberCell.textContent = `BC${Math.floor(Math.random() * 1000000).toString().padStart(6, '0')}`;

      // Create barcode image cell
      const imageCell = document.createElement('td');
      imageCell.className = 'px-6 py-4';
      imageCell.textContent = 'Barcode Image Placeholder'; // Replace with actual barcode image

      // Add cells to row
      row.appendChild(numberCell);
      row.appendChild(imageCell);

      // Add row to table
      tableBody.appendChild(row);
    }
  });

  // Print functionality
  document.getElementById('print-btn').addEventListener('click', function() {
    window.print();
  });
