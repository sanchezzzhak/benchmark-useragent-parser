export default class AppDashboard {
  
  get container(){
    return document.querySelector('#page');
  }
  
  async downloadDbById(id) {
	fetch('/site/reports/' + id + '.zip').then()
  }
  
  renderPage(pageId) {
  
  }
  
}