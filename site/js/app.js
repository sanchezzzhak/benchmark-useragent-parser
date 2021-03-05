import {unzip, setOptions} from 'https://unpkg.com/unzipit@0.1.9/dist/unzipit.module.js';

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

function createElement(type, attributes) {
  const element = document.createElement(type);
  if (attributes) {
	setElementAttrs(element, attributes);
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

function createHtmlElement(html, sanitize = false) {
  return createHtml(html, sanitize).firstChild;
}

let parserDom = null;

function createHtml(html, sanitize = false) {
  if (!parserDom) {
	parserDom = new DOMParser();
  }
  const parsedElement = parserDom.parseFromString(html, 'text/html').body;
  if (sanitize) {
	sanitizeScriptNodes(parsedElement);
	const insecureElements = parsedElement.querySelectorAll('img,svg');
	for (let i = insecureElements.length; i--;) {
	  const element = insecureElements[i];
	  sanitizeElementAttributes(element);
	}
  }
  return parsedElement;
}

/**
 * Delete script nodes
 * @param element
 * @returns {*}
 */
function sanitizeScriptNodes(element) {
  const nodes = element.querySelectorAll('script,object,iframe');
  for (let i = nodes.length; i--;) {
	const node = nodes[i];
	node.parentNode.removeChild(node);
  }
  return element;
}

/**
 * Delete event handler attributes that could execute XSS JavaScript
 * @param element
 * @returns {*}
 */
function sanitizeElementAttributes(element) {
  const attributes = element.attributes;
  for (let i = attributes.length; i--;) {
	const name = attributes[i].name;
	if (/^on/.test(name)) {
	  element.removeAttribute(name);
	}
  }
  return element;
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
		<div id="main" class="page" style="display: none">
		  Select reports
		  <div id="list-reports"></div>
		</div>
	`));
	
	let container = document.getElementById('list-reports');
	reports.forEach((item) => {
	  let obj = createElement('a', {href: '#'})
	  obj.innerText = item;
	  obj.addEventListener('click', this.onSelectReport.bind(this, item))
	  appendElement(container, obj)
	});
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
  
  async importArchive(name) {
	this.stageLock = true;
	try {
	  this.setTextStatus('Download archive ' + name)
	  this.stageLock = true;
	  const {entries} = await unzip('./reports/' + name);
	  
	  let total = await entries['total.json'].json();
	  console.log(total);
	  
	  
	  /*
	  let textReader = new TextReader(await entries['compare-detail.log'].blob());
  
  
	  this.list[db] = new Dexie("name");
	  db.version(10).stores({
		friends: "parent_id,"
	  });
  
  
	  while(true) {
		let line = await textReader.readLine();
		if(line === null) break;
		// PROCESS LINE
	
	  
	  }
		*/
	 
	 
	} catch (e) {
	  this.stageLock = false;
	  console.error(e)
	}
	
	this.stageLock = true;
  }
  
  
}