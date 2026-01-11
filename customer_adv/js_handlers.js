document.addEventListener("DOMContentLoaded", function () {

  // ---------- EDIT BUTTON ----------
  document.querySelectorAll(".editBtn").forEach(btn => {
    btn.addEventListener("click", async function () {
      let id = this.dataset.id;

      try {
        let res = await fetch("customer_adv/get.php?id=" + id);
        let data = await res.json();

        if (!data || !data.id) {
          alert("Error loading customer details");
          return;
        }

        document.getElementById("edit_id").value = data.id;
        document.getElementById("edit_code").value = data.customer_code;
        document.getElementById("edit_name").value = data.full_name;
        document.getElementById("edit_type").value = data.type;
        document.getElementById("edit_phone").value = data.phone;
        document.getElementById("edit_email").value = data.email;

        let modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();

      } catch (e) {
        console.error(e);
        alert("Error contacting server");
      }
    });
  });

  // ---------- SAVE EDIT ----------
  let editForm = document.getElementById("editForm");
  if (editForm) {
    editForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      let formData = new FormData(this);

      try {
        let res = await fetch("customer_adv/update.php", {
          method: "POST",
          body: formData
        });

        let json = await res.json();
        alert(json.message);

        if (json.status === "success") {
          location.reload();
        }

      } catch (e) {
        console.error(e);
        alert("Error saving changes");
      }
    });
  }

  // ---------- DELETE ----------
  document.querySelectorAll(".deleteBtn").forEach(btn => {
    btn.addEventListener("click", async function () {
      if (!confirm("Are you sure you want to delete this customer?")) return;

      let id = this.dataset.id;

      try {
        let res = await fetch("customer_adv/remove.php?id=" + id);
        let json = await res.json();

        alert(json.message);

        if (json.status === "success") {
          let row = document.getElementById("row_" + id);
          if (row) row.remove();
        }

      } catch (e) {
        console.error(e);
        alert("Error contacting server");
      }
    });
  });

});
