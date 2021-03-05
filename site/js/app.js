import {unzip} from 'https://unpkg.com/unzipit@0.1.9/dist/unzipit.module.js';

class TextReader {
  CHUNK_SIZE = 8192000; // I FOUND THIS TO BE BEST FOR MY NEEDS, CAN BE ADJUSTED
  position = 0;
  length = 0;
  
  byteBuffer = new Uint8Array(0);
  
  lines = [];
  lineCount = 0;
  lineIndexTracker = 0;
  
  fileReader = new FileReader();
  textDecoder = new TextDecoder(`utf-8`);
  
  get allCachedLinesAreDispatched() {
	return !(this.lineIndexTracker < this.lineCount);
  }
  
  get blobIsReadInFull() {
	return !(this.position < this.length);
  }
  
  get bufferIsEmpty() {
	return this.byteBuffer.length === 0;
  }
  
  get endOfStream() {
	return this.blobIsReadInFull && this.allCachedLinesAreDispatched && this.bufferIsEmpty;
  }
  
  constructor(blob) {
	this.blob = blob;
	this.length = blob.size;
  }
  
  blob2arrayBuffer(blob) {
	return new Promise((resolve, reject) => {
	  this.fileReader.onerror = reject;
	  this.fileReader.onload = () => {
		resolve(this.fileReader.result);
	  };
	  
	  this.fileReader.readAsArrayBuffer(blob);
	});
  }
  
  read(offset, count) {
	return new Promise(async (resolve, reject) => {
	  if (!Number.isInteger(offset) || !Number.isInteger(count) || count < 1 || offset < 0 || offset > this.length - 1) {
		resolve(new ArrayBuffer(0));
		return
	  }
	  
	  let endIndex = offset + count;
	  
	  if (endIndex > this.length) endIndex = this.length;
	  
	  let blobSlice = this.blob.slice(offset, endIndex);
	  
	  resolve(await this.blob2arrayBuffer(blobSlice));
	});
  }
  
  readLine() {
	return new Promise(async (resolve, reject) => {
	  
	  if (!this.allCachedLinesAreDispatched) {
		resolve(this.lines[this.lineIndexTracker++] + `\n`);
		return;
	  }
	  
	  while (!this.blobIsReadInFull) {
		let arrayBuffer = await this.read(this.position, this.CHUNK_SIZE);
		this.position += arrayBuffer.byteLength;
		
		let tempByteBuffer = new Uint8Array(this.byteBuffer.length + arrayBuffer.byteLength);
		tempByteBuffer.set(this.byteBuffer);
		tempByteBuffer.set(new Uint8Array(arrayBuffer), this.byteBuffer.length);
		
		this.byteBuffer = tempByteBuffer;
		
		let lastIndexOfLineFeedCharacter = this.byteBuffer.lastIndexOf(10); // LINE FEED CHARACTER (\n) IS ONE BYTE LONG IN UTF-8 AND IS 10 IN ITS DECIMAL FORM
		
		if (lastIndexOfLineFeedCharacter > -1) {
		  let lines = this.textDecoder.decode(this.byteBuffer).split(`\n`);
		  this.byteBuffer = this.byteBuffer.slice(lastIndexOfLineFeedCharacter + 1);
		  
		  let firstLine = lines[0];
		  
		  this.lines = lines.slice(1, lines.length - 1);
		  this.lineCount = this.lines.length;
		  this.lineIndexTracker = 0;
		  
		  resolve(firstLine + `\n`);
		  return;
		}
	  }
	  
	  if (!this.bufferIsEmpty) {
		let line = this.textDecoder.decode(this.byteBuffer);
		this.byteBuffer = new Uint8Array(0);
		resolve(line);
		return;
	  }
	  
	  resolve(null);
	});
  }
}


function emptyElement(element) {
  if (!element instanceof Element) {
	return;
  }
  let {length} = element.childNodes;
  while (length > 0) {
	element.removeChild(element.lastChild);
	length -= 1;
  }
}

function createElement(type, attributes, text) {
  const element = document.createElement(type);
  if (attributes) {
	setElementAttrs(element, attributes);
  }
  if (text) {
	element.innerText = text;
  }
  
  return element;
}

function setElementAttrs(element, attributes) {
  Object.entries(attributes)
  .filter(([, value]) => {
	return !(value === null || typeof value === 'undefined')
  })
  .forEach(([key, value]) => element.setAttribute(key, value));
}

function appendElement(parent, target) {
  if (!parent instanceof Element || !target instanceof Element) {
	return;
  }
  parent.appendChild(target);
}

let parserDom = null;

function createHtml(html) {
  if (!parserDom) {
	parserDom = new DOMParser();
  }
  return parserDom.parseFromString(html, 'text/html').body;
}


export default class AppDashboard {
  
  constructor() {
	this.stageLock = false;
	this.renderPageMain();
  }
  
  
  get container() {
	return document.querySelector('#page');
  }
  
  async downloadDbById(id) {
	
	// console.log(entries);
  }
  
  renderPage(pageId) {
	
	document.querySelectorAll('.page').forEach(elm => {
	  elm.style.display = 'none';
	})
	document.getElementById(pageId).style.display = 'block';
  }
  
  renderPageMain() {
	let reports = ['2021-03-03.zip'];
	
	appendElement(this.container, createHtml(`
		<div id="status"></div>
		<hr>
		<div id="main" class="page" style="display: none">
		  <h5>Select reports</h5>
		  <table class="table table-dark table-striped" id="list-reports">
			<thead>
			  <tr>
				<th scope="col">Name</th>
				<th scope="col">Actions</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>
	`));
	
	let container = document.querySelector('#list-reports tbody');
	reports.forEach((item) => {
	  let tr = createElement('tr')
	  let td1 = createElement('td', {}, item)
	  let td2 = createElement('td', {})
	  let viewAction = createElement('a', {class: 'btn btn-primary btn-xs'}, 'View Archive')
	  
	  viewAction.addEventListener('click', this.onSelectReport.bind(this, item))
	  
	  appendElement(tr, td1)
	  appendElement(td2, viewAction)
	  appendElement(tr, td2)
	  appendElement(container, tr)
	});
	
	// bind events
	
	
  }
  
  setTextStatus(text) {
	document.getElementById('status').innerText = text;
  }
  
  
  onSelectReport(name) {
	if (this.stageLock) {
	  return;
	}
	
	if (!this.list) {
	  this.list = {};
	}
	
	this.importArchive(name).then(() => {
	  this.stageLock = false;
	})
	
  }
  
  archiveToInt(name) {
	return parseInt(name.replaceAll('-', ''));
  }
  
  
  async importArchive(name) {
	this.stageLock = true;
	try {
	  
	  let versionDb = this.archiveToInt(name);
	  let dbKey = `db_${versionDb}`;
	  
	  let db = new Dexie("benchmark-useragent-parser");
	  this.list[dbKey] = db;
	  
	  db.version(versionDb).stores({
		useragents: "id",
		details: "++id,parent_id",
		parsers: "++id,name"
	  }, true);
	  
	  const {entries} = await unzip('./reports/' + name);
	  let total = await entries['total.json'].json();
	  for (const key of Object.keys(total)) {
		await db.parsers.add({name: key, data: total[key]});
	  }
	  
	  this.setTextStatus('Download archive ' + name)
	  let textReader = new TextReader(await entries['compare-detail.log'].blob());
	  this.setTextStatus('insert records to db ');
	  
	  while(true) {
		let line = await textReader.readLine();
		if(line === null) break;
		let json = JSON.parse(line);
		await db.useragents.add({id: json.id, user_agent: json.user_agent});
	  }
	  
	  this.stageLock = true;
	} catch (e) {
	  this.stageLock = false;
	  console.error(e)
	}
	
	this.stageLock = true;
  }
  
  
}