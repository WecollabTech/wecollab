<template>
    <div class="search-container">
        <form role="search" class="search-form">
            <input
                type="text"
                name="searchIcon"
                id="searchIcon"
                v-model="searchIcon"
                placeholder="Buscar..."
                class="search-input"
            />
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            searchIcon: "",
        };
    },
};
</script>

<style scoped>
.search-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    margin-top: 20px;
}

.search-form {
    display: flex;
    align-items: center;
    width: 50%;
    max-width: 500px;
    background-color: #fff;
    border-radius: 25px;
    padding: 5px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.search-input {
    flex: 1;
    border: none;
    outline: none;
    padding: 10px 15px;
    font-size: 16px;
    border-radius: 25px 0 0 25px;
}

.search-button {
    background: aqua;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 0 25px 25px 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-button i {
    color: white;
    font-size: 18px;
}

.search-button:hover {
    background: #00cccc;
}
</style>
