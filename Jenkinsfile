pipeline {
    agent any
    stages {
        stage('Build') {
            agent any
			steps {
                script {
                    if (isUnix()) {
                        stage ('Build on Linux server') {
							echo 'Debug: jenkins directory is:'
							sh 'pwd'
							echo 'Setup Composer (Jenkins)'
                            sh 'composer update'
							sh 'composer validate'
							echo 'Setup Composer (project)'
							sh 'cd src'
                            sh 'composer update'
							sh 'composer validate'
							sh 'cd ..'
							echo 'Code Syntax Checking (PHPLINT)'
							sh 'vendor/bin/phplint --exclude=vendor/* src/'
                            
                        }
                    } else {
                        stage ('Build on Windows server') {
                            echo 'Debug: jenkins directory is:'
							echo  '%cd%'
							echo 'Setup Composer...'
							bat 'composer update'
							bat 'composer validate'
							bat 'cd src'
                            bat 'composer update'
							bat 'composer validate'
							bat 'cd ..'
							echo 'Code Syntax Checking (PHPLINT)'
							bat 'vendor/bin/phplint --exclude=vendor/* src/'
							
                        }
                    }
                }
            }
        }
		stage('Test') {
            agent any
			steps {
                script {
                    if (isUnix()) {
                        stage ('Test on Linux server') {
                            try {
                                //--log-junit Log test execution in JUnit XML format to file
                                //--coverage-html Generate code coverage report in HTML format
                                //--coverage-clover Generate code coverage report in Clover XML format
                                sh 'vendor/bin/phpunit test --configuration test/phpunit.xml --log-junit reports/phpunitreport.xml --coverage-html reports/coverage --coverage-clover reports/coverage/phpunitcoverage.xml'
                            }
                            catch (exc) {
                                echo 'PHPUnit - Test fallito!'
                            }
                            finally {
                                xunit testTimeMargin: '3000', thresholdMode: 1, thresholds: [failed(failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0'), skipped(failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0')], tools: [PHPUnit(deleteOutputFiles: false, failIfNotNew: false, pattern: 'reports/phpunitreport.xml', skipNoTestFiles: true, stopProcessingIfError: false)]
                            }
                        }
                    } else {
                        stage ('Test on Windows server') {
							try {
								//--log-junit Log test execution in JUnit XML format to file
								//--coverage-html Generate code coverage report in HTML format
								//--coverage-clover Generate code coverage report in Clover XML format
								bat 'phpunit test --configuration test/phpunit.xml --log-junit reports/phpunitreport.xml --coverage-html reports/coverage --coverage-clover reports/coverage/phpunitcoverage.xml'
							}
							catch (exc) {
								echo 'PHPUnit - Test fallito!'
							}
							finally {
								xunit testTimeMargin: '3000', thresholdMode: 1, thresholds: [failed(failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0'), skipped(failureNewThreshold: '0', failureThreshold: '0', unstableNewThreshold: '0', unstableThreshold: '0')], tools: [PHPUnit(deleteOutputFiles: false, failIfNotNew: false, pattern: 'reports/phpunitreport.xml', skipNoTestFiles: true, stopProcessingIfError: false)]
							}
                        }
                    }
                }
            }
        }
        stage('Code quality analysis') {
            agent any
			steps {
                script {
                    if (isUnix()) {
                        stage ('Analysis on Linux server') {
                            echo 'Items count (PHPLOC)'
                            sh 'vendor/bin/phploc --count-tests --log-csv reports/phploc.csv --log-xml reports/phploc.xml --exclude=_Files,nbproject,vendor src/*'
                            // plotta i dati di phploc prendendo il file reports/phploc.csv
                            // Su jenkins esce il tasto PLOTS
                            plot csvFileName: 'plot-graphic-php-loc.csv', csvSeries: [[displayTableFlag: false, exclusionValues: '', file: 'reports/phploc.csv', inclusionFlag: 'OFF', url: '']], group: 'PHPLoc', style: 'line', title: 'Plot PHPLoc', exclZero: false, keepRecords: true, logarithmic: false, numBuilds: '10', useDescr: false, yaxis: '', yaxisMaximum: '7500', yaxisMinimum: ''
                            
							stage ('SonarQube') {
                                //-X abilita le stampe debug
                                //-Dsonar.log.level=INFO 
                                script {
                                    scannerHome = tool 'SonarQube Scanner';
                                }
                                withSonarQubeEnv('SonarQube Server') {
                                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.exclusions=**/vendor/**,**/test/**,_Files/*,nbproject/*,**/reports/** -Dsonar.php.coverage.reportPath=reports/coverage/phpunitcoverage.xml -Dsonar.php.tests.reportPath=reports/phpunitreport.xml -Dsonar.projectKey=ingsw2 -Dsonar.sources=src/ -Dsonar.host.url=http://localhost:9000 -Dsonar.login=cca5d70f432994276695c8fab78c253b8fd48e4f"
                                }
                            }
                        }
                    } else {
                        stage ('Analysis on Windows server') {
                            echo 'Items count (PHPLOC)'
                            bat 'phploc --count-tests --log-csv reports/phploc.csv --log-xml reports/phploc.xml --exclude=_Files,nbproject,vendor src/*'
							// plotta i dati di phploc prendendo il file reports/phploc.csv
							// Su jenkins esce il tasto PLOTS
							plot csvFileName: 'plot-a9e8694a-4c3f-4081-9837-28740e3907f6.csv', csvSeries: [[displayTableFlag: false, exclusionValues: '', file: 'reports/phploc.csv', inclusionFlag: 'OFF', url: '']], group: 'PHPLoc', style: 'line', title: 'Plot PHPLoc', exclZero: false, keepRecords: true, logarithmic: false, numBuilds: '', useDescr: false, yaxis: '', yaxisMaximum: '10000', yaxisMinimum: ''
							
							stage ('SonarQube') {
								//-X abilita le stampe debug
								//-Dsonar.log.level=INFO 
								script {
									scannerHome = tool 'SonarQube Scanner';
								}
								withSonarQubeEnv('SonarQube Server') {
									bat "${scannerHome}/bin/sonar-scanner.bat -Dsonar.exclusions=**/vendor/**,**/test/**,_Files/*,nbproject/*,**/reports/** -Dsonar.php.coverage.reportPath=reports/coverage/phpunitcoverage.xml -Dsonar.php.tests.reportPath=reports/phpunitreport.xml -Dsonar.projectKey=ingsw2 -Dsonar.sources=src/ -Dsonar.host.url=http://localhost:9000 -Dsonar.login=606e81f78b6ca2d210e4d4d3c50781a6948e84eb"
									// 2bb64245dfa3edfb16a36ab1661e09a199738372  606e81f78b6ca2d210e4d4d3c50781a6948e84eb
								}
                            }
                        }
                    }
                }
            }
        }
        stage('Sanity check') {
            steps {
                input "Do you want to proceed with the deploy?"
            }
        }
        stage('Deploy') {
            agent any
            steps {
				script {
					echo 'Deploying....'
					ftpPublisher alwaysPublishFromMaster: false, continueOnError: false, failOnError: false, publishers: [[configName: 'CoPPS', transfers: [[asciiMode: false, cleanRemote: false, excludes: '', flatten: false, makeEmptyDirs: false, noDefaultExcludes: false, patternSeparator: '[, ]+', remoteDirectory: '', remoteDirectorySDF: false, removePrefix: 'src/', sourceFiles: 'src/**']], usePromotionTimestamp: false, useWorkspaceInPromotion: false, verbose: false]]
				}
            }
            post {
                always {
                    echo 'Finish!!!'
                }
            }
        }
    }
}