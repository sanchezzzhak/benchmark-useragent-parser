import {unzip, setOptions} from 'https://unpkg.com/unzipit@0.1.9/dist/unzipit.module.js';


export default class AppDashboard {
  
  get container(){
    return document.querySelector('#page');
  }
  
  async downloadDbById(id) {
	// const {entries} = unzip('./reports/fixture--browser-detector.zip');
	// console.log(entries);
  }
  
  renderPage(pageId) {
  
  }
  
}