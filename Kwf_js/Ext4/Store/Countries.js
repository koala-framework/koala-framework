Ext4.define('Kwf.Ext4.Store.Countries', {
    extend: 'Ext.data.Store',
    requires: [ 'Kwf.Ext4.Store.CountriesModel', 'Kwf.CountriesData' ],
    model: 'Kwf.Ext4.Store.CountriesModel',
    proxy: null,
    sorters: [{
         property: 'name',
         direction: 'ASC'
    }]
}).create({
    storeId: 'countries',
    data: Kwf.CountriesData //set in Kwf_Assets_Dependency_Dynamic_CountriesData
});
