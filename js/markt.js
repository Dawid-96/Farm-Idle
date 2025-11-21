// // Markt-Tab Verwaltung (Vereinfacht)

function renderMarket() {
  const container = document.querySelector("#market-list");
  const template = document.querySelector("#tpl-market");

  if (!container || !template) {
    console.error("Markt-Container oder Template nicht gefunden");
    return;
  }

  container.innerHTML = "";

  for (const [itemId, item] of Object.entries(CONFIG.ITEMS)) {
    const stock = window.state.inventory[itemId] || 0;
    const price = item.basePrice;

    const card = template.content.cloneNode(true);

    card.querySelector("[data-ref='itemName']").textContent = item.name;
    card.querySelector("[data-ref='price']").textContent = price.toFixed(2);
    card.querySelector("[data-ref='icon']").src = item.icon;
    card.querySelector("[data-ref='icon']").alt = item.name;
    card.querySelector("[data-ref='stock']").textContent = stock;

    const qtyInput = card.querySelector("[data-ref='qty']");
    const sellBtn = card.querySelector("[data-action='sell']");

    if (stock <= 0) {
      sellBtn.disabled = true;
      qtyInput.disabled = true;
      qtyInput.value = 0;
    } else {
      qtyInput.max = stock;
      qtyInput.value = 1;
      qtyInput.min = 1;

      sellBtn.onclick = () => {
        const qty = parseInt(qtyInput.value) || 0;
        sellItem(itemId, qty);
      };
    }

    container.appendChild(card);
  }
}

// Item Verkaufen
function sellItem(itemId, quantity) {
  const stock = window.state.inventory[itemId] || 0;

  // Sicherheits validierung
  if (quantity <= 0 || quantity > stock) {
    console.warn(
      `Ungültiger Versuch: ${itemId}x ${quantity} Bestand: ${stock}`
    );
    renderMarket();
    return;
  }

  // Preis berechnen
  const price = CONFIG.ITEMS[itemId].basePrice;
  const totalEarnings = price * quantity;

  // Verkauf durchführen
  window.state.inventory[itemId] -= quantity;
  Game.addGold(totalEarnings);

  Storage.saveGameState(window.state);
  renderMarket();
  updateDisplay();
}

  // Initialisierung
  document.addEventListener("DOMContentLoaded", () => {
    const marketTab = document.querySelector("[data-tab='tab-markt']");

    if (!marketTab) {
      console.warn("Markt-Tab nicht gefunden");
      return;
    }
    // Event listener für Tab-Klick
    marketTab.addEventListener("click", () => {
      renderMarket();
    });
  });

// Export
window.Market = {
  render: renderMarket,
  sell: sellItem,
};
