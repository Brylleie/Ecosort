const totalUsersElement = document.getElementById('totalUsers');
const totalSalesElement = document.getElementById('totalSales');
const alertButton = document.getElementById('alertButton');

alertButton.addEventListener('click', () => {
    const totalUsers = totalUsersElement.textContent;
    const totalSales = totalSalesElement.textContent;
    alert(`Total Users: ${totalUsers}, Total Sales: ${totalSales}`);
});