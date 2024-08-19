let dragSrcEl = null;
let table_set = false


function aggregateBudgets(data) {
    const combinedResult = {
        head: [],
        body: [],
        footer: null,
        totalBudget: 0 // Initialize total budget
    };

    const budgetMap = {};

    data.forEach(item => {
        const parsedData = JSON.parse(item.data);

        // Initialize the header if it's not set yet
        if (combinedResult.head.length === 0) {
            combinedResult.head = parsedData.head;
        }

        // Handle body as either array or object
        const body = Array.isArray(parsedData.body) ? parsedData.body : Object.values(parsedData.body);

        const itemCodeIndex = parsedData.head.indexOf("Item-code");
        const budgetIndex = parsedData.head.indexOf("buget");

        body.forEach(row => {
            const itemCode = row[itemCodeIndex];
            const budgetValue = parseFloat(row[budgetIndex]) || 0;

            if (budgetMap[itemCode]) {
                budgetMap[itemCode].budget += budgetValue;
            } else {
                budgetMap[itemCode] = {
                    row: [...row],
                    budgetIndex: budgetIndex,
                    budget: budgetValue
                };
            }
        });
    });

    let rowIndex = 1;
    for (const itemCode in budgetMap) {
        const item = budgetMap[itemCode];
        item.row[0] = String(rowIndex); // Update Row-Number dynamically
        item.row[item.budgetIndex] = String(item.budget); // Update budget
        combinedResult.body.push(item.row);
        combinedResult.totalBudget += item.budget; // Accumulate total budget
        rowIndex++;
    }

    return combinedResult;
}


// function aggregateData(data) {
//     const result = {
//         head: [],
//         body: [],
//         footer: null,
//         totalBudget: 0
//     };

//     const columnIndexes = {};
//     const rows = {};
//     const seenColumns = new Set();

//     data.forEach(entry => {
//         const parsedData = JSON.parse(entry.data);
//         const { head, body } = parsedData;

//         // Update columns list
//         head.forEach((column, index) => {
//             if (!seenColumns.has(column)) {
//                 seenColumns.add(column);
//                 result.head.push(column);
//                 columnIndexes[column] = index;
//             }
//         });

//         // Process rows
//         body.forEach(row => {
//             const itemCode = row[columnIndexes['Item-code']];
//             if (!rows[itemCode]) {
//                 rows[itemCode] = Array(result.head.length).fill('');
//                 rows[itemCode][columnIndexes['Row-Number']] = row[columnIndexes['Row-Number']];
//                 rows[itemCode][columnIndexes['Item-code']] = itemCode;
//             }

//             // Aggregate buget
//             const budgetIndex = columnIndexes['buget'];
//             if (budgetIndex !== undefined && row[budgetIndex]) {
//                 rows[itemCode][budgetIndex] = (parseFloat(rows[itemCode][budgetIndex] || 0) + parseFloat(row[budgetIndex])).toFixed(2);
//             }

//             // Add additional columns
//             for (let column in columnIndexes) {
//                 if (column !== 'Item-code' && column !== 'Row-Number' && row[columnIndexes[column]] !== undefined) {
//                     rows[itemCode][columnIndexes[column]] = row[columnIndexes[column]];
//                 }
//             }
//         });

//         // Add total budget
//         data.forEach(entry => {
//             const parsedData = JSON.parse(entry.data);
//             parsedData.body.forEach(row => {
//                 const budgetIndex = columnIndexes['buget'];
//                 if (budgetIndex !== undefined) {
//                     result.totalBudget += parseFloat(row[budgetIndex] || 0);
//                 }
//             });
//         });

//         // Convert rows to array
//         result.body = Object.values(rows);
//     });

//     return result;
// }


function dragStart(event) {
    dragSrcEl = event.target.closest('th');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', dragSrcEl.outerHTML);
}

function allowDrop(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
}

function drop(event) {
    event.preventDefault();
    const target = event.target.closest('th');
    if (dragSrcEl !== target) {
        dragSrcEl.parentNode.removeChild(dragSrcEl);
        const dropHTML = event.dataTransfer.getData('text/html');
        target.insertAdjacentHTML('beforebegin', dropHTML);

        // Reinitialize drag-and-drop event listeners for the new element
        const newHeader = target.previousSibling;
        addDragDropHandlers(newHeader);
    }
}

function addDragDropHandlers(element) {
    element.setAttribute('draggable', 'true');
    element.ondragstart = dragStart;
    element.ondragover = allowDrop;
    element.ondrop = drop;
}
function addRow() {
    const table = document.getElementById('itemTable');
    const headerCells = table.querySelectorAll('thead th');
    const newRow = table.insertRow();
    const rowCount = table.rows.length - 1; // Adjust for the header row

    headerCells.forEach((headerCell, index) => {
        const newCell = newRow.insertCell(index);

        if (index === 0) {
            // Row number cell
            const rowNumberInput = document.createElement('input');
            rowNumberInput.type = 'text';
            rowNumberInput.value = rowCount;
            // rowNumberInput.disabled = true; // Make row number read-only
            newCell.appendChild(rowNumberInput);
        } else if (index === headerCells.length - 1) {
            // Action column cell
            const deleteButton = document.createElement('button');
            deleteButton.classList.add('button');
            deleteButton.textContent = 'Delete';
            deleteButton.onclick = function () { deleteRow(this); };
            newCell.appendChild(deleteButton);
        } else {
            // Data input cell
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.name = `rows[${rowCount}][]`;
            inputField.placeholder = `Enter ${headerCell.querySelector('input').value}`;
            newCell.appendChild(inputField);
        }
    });
}


function addColumn() {
    const table = document.getElementById('itemTable');
    const headerRow = table.querySelector('thead tr');
    const newColumnName = prompt('Enter column name:');

    if (newColumnName) {
        const newHeaderCell = document.createElement('th');
        addDragDropHandlers(newHeaderCell);

        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.name = 'headers[]';
        inputField.placeholder = newColumnName;
        inputField.value = newColumnName;

        newHeaderCell.appendChild(inputField);

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () { deleteColumn(newHeaderCell.cellIndex); };
        newHeaderCell.appendChild(deleteButton);

        headerRow.insertBefore(newHeaderCell, headerRow.lastElementChild);

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row) => {
            const newCell = row.insertCell(row.cells.length - 1);
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.name = `rows[${row.rowIndex}][]`;
            inputField.placeholder = `Enter ${newColumnName}`;
            newCell.appendChild(inputField);
        });
    }
}

function deleteRow(button) {
    const row = button.closest('tr');
    row.parentNode.removeChild(row);

}

function deleteColumn(columnIndex) {
    const table = document.getElementById('itemTable');
    const rows = table.rows;

    for (let i = 0; i < rows.length; i++) {
        rows[i].deleteCell(columnIndex);
    }
}

function saveTable() {
    const tableData = getTableData();
    localStorage.setItem('tableData', JSON.stringify(tableData));
    alert('Table data saved locally!');
}

function submitTable() {
    const tableData = getTableData();
    console.log('Submitting table data:', tableData);
    alert('Table data submitted!');
}

function getTableData() {
    const table = document.getElementById('itemTable');
    const headers = Array.from(table.querySelectorAll('thead th input')).map(input => input.value);
    const rows = Array.from(table.querySelectorAll('tbody tr')).map(row =>
        Array.from(row.querySelectorAll('td input')).map(input => input.value)
    );
    return { headers, rows };
}



// Function to create buttons and display table data
function createButtonsAndDisplayData() {
    const buttonContainer = document.getElementById('buttonContainerpropsal');

    // Clear existing buttons
    buttonContainer.innerHTML = '';

    // Iterate over window.data and create buttons
    if (window.data && window.data.length > 0) {

        if (!Array.isArray(window.data)) {
            window.data = [window.data]
            table_set = true
        }

        window.data.forEach((item, index) => {
            item.forEach((item, index) => {
                const button = document.createElement('button');
                button.textContent = `code: ${item.code} [ ${item.time}]`;
                button.classList.add('btn', 'btn-primary', 'm-2');

                // Add click event listener to display table data
                button.addEventListener('click', (event) => {
                    document.getElementById("code").value = item.id
                    event.preventDefault();
                    displayTableData(JSON.parse(item.data));
                });

                // Append button to the container
                buttonContainer.appendChild(button);
            })
        });

    } else {
        // No data available, display a sample table
        displayExampleTable();
    }
}

// Function to display table data
function displayTableData(dataItem) {
    const table = document.getElementById('itemTable');
    const headerRow = table.querySelector('thead tr');
    const tbody = table.querySelector('tbody');

    // Clear existing table content
    headerRow.innerHTML = '';
    tbody.innerHTML = '';

    // Populate headers
    dataItem.head.forEach(header => {
        const th = document.createElement('th');
        addDragDropHandlers(th);

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'headers[]';
        input.placeholder = header;
        input.value = header;

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = () => deleteColumn(th.cellIndex);

        th.appendChild(input);
        th.appendChild(deleteButton);
        headerRow.appendChild(th);
    });

    // Convert dataItem.body to an array if it's not already
    const bodyArray = Array.isArray(dataItem.body) ? dataItem.body : Object.values(dataItem.body);

    // Determine the number of columns from the headers
    const columnCount = dataItem.head.length;

    // Populate rows
    bodyArray.forEach((row, rowIndex) => {
        const tr = document.createElement('tr');

        // Ensure each row has the correct number of cells
        for (let cellIndex = 0; cellIndex < columnCount - 1; cellIndex++) {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `rows[${rowIndex}][]`;
            input.placeholder = 'Enter value';
            input.value = row[cellIndex] || '';  // Use existing value or empty string
            td.appendChild(input);
            tr.appendChild(td);
        }

        // Add delete button at the end of the row
        const deleteTd = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () {
            tr.remove();

        };
        deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);

        tbody.appendChild(tr);
    });

    // Adjust rows if any rows are missing (empty rows)
    const rowCount = bodyArray.length;
    for (let i = rowCount; i < rowCount; i++) {
        const tr = document.createElement('tr');

        for (let cellIndex = 0; cellIndex < columnCount; cellIndex++) {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `rows[${i}][]`;
            input.placeholder = 'Enter value';
            td.appendChild(input);
            tr.appendChild(td);
        }

        // Add delete button at the end of the empty row
        const deleteTd = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () {
            tr.remove();

        };
        deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);

        tbody.appendChild(tr);
    }
}

// Function to display an example table if no data exists
function displayExampleTable() {
    const table = document.getElementById('itemTable');
    const headerRow = table.querySelector('thead tr');
    const tbody = table.querySelector('tbody');

    // Example headers and data
    const exampleHeaders = ['Row-Number', 'Item-code', 'buget', "action"];
    const exampleRows = [
        ['1', '123', '100',],
        ['2', '456', '50',],
        ['3', '789', '80',]
    ];

    // Populate headers
    headerRow.innerHTML = '';
    exampleHeaders.forEach(header => {
        const th = document.createElement('th');
        addDragDropHandlers(th);

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'headers[]';
        input.placeholder = header;
        input.value = header;

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = () => deleteColumn(th.cellIndex);

        th.appendChild(input);
        th.appendChild(deleteButton);
        headerRow.appendChild(th);
    });

    // Populate rows
    exampleRows.forEach((row, rowIndex) => {
        const tr = document.createElement('tr');
        row.forEach(cellData => {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `rows[${rowIndex}][]`;
            input.placeholder = 'Enter value';
            input.value = cellData;
            td.appendChild(input);
            tr.appendChild(td);
        });

        // Optional: Add delete button for each row
        const deleteTd = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('button');
        deleteButton.textContent = 'Delete';
        deleteButton.onclick = function () {
            tr.remove();

        };
        deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);

        tbody.appendChild(tr);
    });
}



// Initialize buttons and table on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {

    createButtonsAndDisplayData();
    if (!Array.isArray(window.data)) {
        displayExampleTable();
    } else {
        const buttonContainer = document.getElementById('buttonContainerpropsal');
        const combinedResult = aggregateBudgets(data[0]);
        document.getElementById("code").value = "total"
        document.getElementById('total').value = "total buget :" + combinedResult.totalBudget
        displayTableData(combinedResult);

        const button = document.createElement('button');
        button.textContent = `Total propsal`;
        button.classList.add('btn', 'btn-primary', 'm-2');

        // Add click event listener to display table data
        button.addEventListener('click', (event) => {
            event.preventDefault();
            document.getElementById("code").value = "total";
            (combinedResult) ? displayTableData(combinedResult) : displayExampleTable();
        });

        // Append button to the container
        buttonContainer.appendChild(button);
    }

});

var sp = location.pathname.split("/")

if (sp.includes('director.php')) {
    displayExampleTable();
}