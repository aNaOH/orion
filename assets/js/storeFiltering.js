const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("search");
const filterForm = document.getElementById("filterForm");

function applyFilter(page = 1) {
  const formData = new FormData(filterForm);
  if (searchInput.value.trim() !== "") {
    formData.append("search", searchInput.value);
  }
  if (formData.get("genre") == "all") {
    formData.delete("genre");
  }
  if (page > 1) {
    formData.append("page", page);
  }
  const params = new URLSearchParams();

  // Pasamos todos los valores del form
  for (const [key, value] of formData.entries()) {
    if (key === "features[]") {
      // Acumulamos features en un array
      if (!params.has("features")) {
        params.set("features", value);
      } else {
        params.set("features", params.get("features") + "," + value);
      }
    } else {
      params.append(key, value);
    }
  }
  const paramsString = params.toString();
  if (paramsString.trim().length > 0) {
    window.location.href = `/store/games?${params.toString()}`;
  } else {
    window.location.href = `/store/games`;
  }
}

searchForm.addEventListener("submit", (event) => {
  event.preventDefault();
  applyFilter();
});

filterForm.addEventListener("submit", (event) => {
  event.preventDefault();
  applyFilter();
});

const previousBtn = document.getElementById("prevPage");
const nextBtn = document.getElementById("nextPage");

if (previousBtn) {
  previousBtn.addEventListener("click", () => {
    const match = window.location.search.match(/page=(\d+)/);
    const currentPage = match ? parseInt(match[1]) : 1;
    applyFilter(currentPage - 1);
  });
}

if (nextBtn) {
  nextBtn.addEventListener("click", () => {
    const match = window.location.search.match(/page=(\d+)/);
    const currentPage = match ? parseInt(match[1]) : 1;
    applyFilter(currentPage + 1);
  });
}
